<?php

namespace App\Services;

use App\Models\PickupRequest;
use App\Models\PickupRequestItem;
use App\Models\BuyerRating;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PickupService
{
    protected $buyerRatingService;
    protected $notificationService;
    protected $walletService;

    public function __construct(
        BuyerRatingService $buyerRatingService,
        NotificationService $notificationService,
        WalletService $walletService
    ) {
        $this->buyerRatingService = $buyerRatingService;
        $this->notificationService = $notificationService;
        $this->walletService = $walletService;
    }

    public function getUserPickups(int $userId): Collection
    {
        return PickupRequest::where('user_id', $userId)
            ->with(['items.product', 'buyerRating'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPickupById(int $id): ?PickupRequest
    {
        return PickupRequest::with(['items.product', 'buyerRating', 'user.sellerProfile'])
            ->find($id);
    }

    public function createPickupRequest(array $data, array $items): PickupRequest
    {
        return DB::transaction(function () use ($data, $items) {
            // Generate pickup code
            $data['pickup_code'] = $this->generatePickupCode();
            $data['requested_at'] = now();
            $data['status'] = 'pending';

            // Create pickup request
            $pickup = PickupRequest::create($data);

            // Create pickup items
            foreach ($items as $item) {
                $item['pickup_request_id'] = $pickup->id;
                PickupRequestItem::create($item);
            }

            // Check buyer rating for risk assessment
            $buyerRating = $this->buyerRatingService->getRatingByPhone($data['recipient_phone']);
            if ($buyerRating && $buyerRating->isHighRisk()) {
                $this->notificationService->createForUser(
                    $pickup->user_id,
                    'high_risk_buyer',
                    'Peringatan Buyer Berisiko Tinggi',
                    $buyerRating->risk_warning
                );
            }

            // Create notification
            $this->notificationService->createPickupNotification(
                $pickup->user_id,
                $pickup->pickup_code,
                'requested'
            );

            return $pickup->load('items.product');
        });
    }

    public function updatePickupStatus(int $pickupId, string $status, array $additionalData = []): PickupRequest
    {
        return DB::transaction(function () use ($pickupId, $status, $additionalData) {
            $pickup = PickupRequest::findOrFail($pickupId);

            $updateData = array_merge(['status' => $status], $additionalData);

            // Set timestamp based on status
            switch ($status) {
                case 'picked_up':
                    $updateData['picked_up_at'] = now();
                    break;
                case 'delivered':
                    $updateData['delivered_at'] = now();
                    // Update buyer rating
                    $this->buyerRatingService->updateOrderStats($pickup->recipient_phone, 'delivered');
                    // Process COD payment if applicable
                    if ($pickup->payment_method === 'cod' && $pickup->cod_amount > 0) {
                        $this->walletService->addBalance(
                            $pickup->user_id,
                            $pickup->cod_amount,
                            "COD dari pickup {$pickup->pickup_code}",
                            'cod_payment',
                            $pickup->id
                        );
                        $updateData['cod_collected_at'] = now();
                    }
                    break;
                case 'cancelled':
                    $this->buyerRatingService->updateOrderStats($pickup->recipient_phone, 'cancelled');
                    break;
                case 'failed':
                    $this->buyerRatingService->updateOrderStats($pickup->recipient_phone, 'failed_cod');
                    break;
            }

            $pickup->update($updateData);

            // Create notification
            $this->notificationService->createPickupNotification(
                $pickup->user_id,
                $pickup->pickup_code,
                $status
            );

            return $pickup;
        });
    }

    public function cancelPickup(int $pickupId, string $reason = null): PickupRequest
    {
        $additionalData = $reason ? ['notes' => $reason] : [];
        return $this->updatePickupStatus($pickupId, 'cancelled', $additionalData);
    }

    public function getPickupsByStatus(string $status, int $userId = null): Collection
    {
        $query = PickupRequest::where('status', $status)->with(['items.product', 'user']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getPickupStats(int $userId = null): array
    {
        $query = PickupRequest::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $total = $query->count();
        $pending = $query->where('status', 'pending')->count();
        $processing = $query->where('status', 'processing')->count();
        $delivered = $query->where('status', 'delivered')->count();
        $cancelled = $query->where('status', 'cancelled')->count();

        $totalRevenue = PickupRequest::where('status', 'delivered')
            ->when($userId, function ($q) use ($userId) {
                return $q->where('user_id', $userId);
            })
            ->sum('cod_amount');

        return [
            'total' => $total,
            'pending' => $pending,
            'processing' => $processing,
            'delivered' => $delivered,
            'cancelled' => $cancelled,
            'success_rate' => $total > 0 ? round(($delivered / $total) * 100, 2) : 0,
            'total_revenue' => $totalRevenue
        ];
    }

    public function searchPickups(string $search, int $userId = null): Collection
    {
        $query = PickupRequest::with(['items.product', 'user'])
            ->where(function ($q) use ($search) {
                $q->where('pickup_code', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('recipient_phone', 'like', "%{$search}%");
            });

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    protected function generatePickupCode(): string
    {
        $date = now()->format('ymd');
        $count = PickupRequest::whereDate('created_at', today())->count() + 1;
        return 'PU' . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
