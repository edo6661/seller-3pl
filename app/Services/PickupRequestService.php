<?php

namespace App\Services;

use App\Models\PickupRequest;
use App\Models\PickupRequestItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PickupRequestService
{
    public function getUserPickupRequests(int $userId): Collection
    {
        return PickupRequest::where('user_id', $userId)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPendingPickupRequests(int $userId): Collection
    {
        return PickupRequest::where('user_id', $userId)
            ->pending()
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getConfirmedPickupRequests(int $userId): Collection
    {
        return PickupRequest::where('user_id', $userId)
            ->confirmed()
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPickupRequestById(int $id): ?PickupRequest
    {
        return PickupRequest::with(['items.product', 'user'])->find($id);
    }

    public function createPickupRequest(array $data): PickupRequest
    {
        return DB::transaction(function () use ($data) {
            // Hitung total dari items
            $productTotal = 0;
            $totalWeight = 0;

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $totalPrice = $item['quantity'] * $product->price;
                $itemWeight = $item['quantity'] * $product->weight_per_pcs;
                
                $productTotal += $totalPrice;
                $totalWeight += $itemWeight;
            }

            // Data pickup request
            $pickupData = [
                'user_id' => $data['user_id'],
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => $data['recipient_phone'],
                'recipient_city' => $data['recipient_city'],
                'recipient_province' => $data['recipient_province'],
                'recipient_postal_code' => $data['recipient_postal_code'],
                'recipient_address' => $data['recipient_address'],
                'recipient_latitude' => $data['recipient_latitude'] ?? 0,
                'recipient_longitude' => $data['recipient_longitude'] ?? 0,
                'pickup_name' => $data['pickup_name'],
                'pickup_phone' => $data['pickup_phone'],
                'pickup_city' => $data['pickup_city'],
                'pickup_province' => $data['pickup_province'],
                'pickup_postal_code' => $data['pickup_postal_code'],
                'pickup_address' => $data['pickup_address'],
                'pickup_latitude' => $data['pickup_latitude'] ?? 0,
                'pickup_longitude' => $data['pickup_longitude'] ?? 0,
                'pickup_scheduled_at' => $data['pickup_scheduled_at'] ?? null,
                'payment_method' => $data['payment_method'],
                'shipping_cost' => $data['shipping_cost'],
                'service_fee' => $data['service_fee'] ?? 0,
                'product_total' => $productTotal,
                'cod_amount' => $data['payment_method'] === 'cod' ? $productTotal : 0,
                'total_amount' => $productTotal + $data['shipping_cost'] + ($data['service_fee'] ?? 0),
                'status' => 'pending',
                'courier_service' => $data['courier_service'] ?? null,
                'notes' => $data['notes'] ?? null,
            ];

            $pickupRequest = PickupRequest::create($pickupData);

            // Buat items
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                PickupRequestItem::create([
                    'pickup_request_id' => $pickupRequest->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'weight_per_pcs' => $product->weight_per_pcs,
                    'price_per_pcs' => $product->price,
                ]);
            }

            return $pickupRequest->load(['items.product']);
        });
    }

    public function updatePickupRequest(int $id, array $data): PickupRequest
    {
        return DB::transaction(function () use ($id, $data) {
            $pickupRequest = PickupRequest::findOrFail($id);
            
            // Update data pickup request
            $pickupRequest->update($data);

            // Jika ada items baru, update items
            if (isset($data['items'])) {
                // Hapus items lama
                $pickupRequest->items()->delete();

                $productTotal = 0;
                
                // Buat items baru
                foreach ($data['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $totalPrice = $item['quantity'] * $product->price;
                    $productTotal += $totalPrice;
                    
                    PickupRequestItem::create([
                        'pickup_request_id' => $pickupRequest->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'weight_per_pcs' => $product->weight_per_pcs,
                        'price_per_pcs' => $product->price,
                    ]);
                }

                // Update total
                $pickupRequest->update([
                    'product_total' => $productTotal,
                    'cod_amount' => $pickupRequest->payment_method === 'cod' ? $productTotal : 0,
                    'total_amount' => $productTotal + $pickupRequest->shipping_cost + $pickupRequest->service_fee,
                ]);
            }

            return $pickupRequest->load(['items.product']);
        });
    }

    public function cancelPickupRequest(int $id): PickupRequest
    {
        $pickupRequest = PickupRequest::findOrFail($id);
        
        if (!$pickupRequest->canBeCancelled()) {
            throw new \Exception('Pickup request tidak dapat dibatalkan');
        }

        $pickupRequest->update(['status' => 'cancelled']);
        
        return $pickupRequest;
    }

    public function confirmPickupRequest(int $id): PickupRequest
    {
        $pickupRequest = PickupRequest::findOrFail($id);
        
        if ($pickupRequest->status !== 'pending') {
            throw new \Exception('Hanya pickup request dengan status pending yang dapat dikonfirmasi');
        }

        $pickupRequest->update(['status' => 'confirmed']);
        
        return $pickupRequest;
    }

    public function schedulePickup(int $id, string $scheduledAt): PickupRequest
    {
        $pickupRequest = PickupRequest::findOrFail($id);
        
        if ($pickupRequest->status !== 'confirmed') {
            throw new \Exception('Pickup request harus dikonfirmasi terlebih dahulu');
        }

        $pickupRequest->update([
            'status' => 'pickup_scheduled',
            'pickup_scheduled_at' => $scheduledAt
        ]);
        
        return $pickupRequest;
    }

    public function markAsPickedUp(int $id, array $courierData = []): PickupRequest
    {
        $pickupRequest = PickupRequest::findOrFail($id);
        
        $updateData = [
            'status' => 'picked_up',
            'picked_up_at' => now()
        ];

        if (!empty($courierData)) {
            $updateData['courier_tracking_number'] = $courierData['tracking_number'] ?? null;
            $updateData['courier_response'] = $courierData['response'] ?? null;
        }

        $pickupRequest->update($updateData);
        
        return $pickupRequest;
    }

    public function markAsInTransit(int $id): PickupRequest
    {
        $pickupRequest = PickupRequest::findOrFail($id);
        
        if ($pickupRequest->status !== 'picked_up') {
            throw new \Exception('Pickup request harus sudah diambil terlebih dahulu');
        }

        $pickupRequest->update(['status' => 'in_transit']);
        
        return $pickupRequest;
    }

    public function markAsDelivered(int $id): PickupRequest
    {
        $pickupRequest = PickupRequest::findOrFail($id);
        
        $updateData = [
            'status' => 'delivered',
            'delivered_at' => now()
        ];

        // Jika COD, tandai sebagai collected
        if ($pickupRequest->payment_method === 'cod') {
            $updateData['cod_collected_at'] = now();
        }

        $pickupRequest->update($updateData);
        
        return $pickupRequest;
    }

    public function markAsFailed(int $id, string $reason = null): PickupRequest
    {
        $pickupRequest = PickupRequest::findOrFail($id);
        
        $pickupRequest->update([
            'status' => 'failed',
            'notes' => $reason ? ($pickupRequest->notes . ' | Failed: ' . $reason) : $pickupRequest->notes
        ]);
        
        return $pickupRequest;
    }

    public function getPickupRequestsByStatus(int $userId, string $status): Collection
    {
        return PickupRequest::where('user_id', $userId)
            ->where('status', $status)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchPickupRequests(string $search, int $userId): Collection
    {
        return PickupRequest::where('user_id', $userId)
            ->where(function ($query) use ($search) {
                $query->where('pickup_code', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('recipient_phone', 'like', "%{$search}%")
                    ->orWhere('courier_tracking_number', 'like', "%{$search}%");
            })
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPickupRequestStats(int $userId): array
    {
        $total = PickupRequest::where('user_id', $userId)->count();
        $pending = PickupRequest::where('user_id', $userId)->pending()->count();
        $confirmed = PickupRequest::where('user_id', $userId)->confirmed()->count();
        $pickupScheduled = PickupRequest::where('user_id', $userId)->pickupScheduled()->count();
        $pickedUp = PickupRequest::where('user_id', $userId)->pickedUp()->count();
        $inTransit = PickupRequest::where('user_id', $userId)->inTransit()->count();
        $delivered = PickupRequest::where('user_id', $userId)->delivered()->count();
        $failed = PickupRequest::where('user_id', $userId)->failed()->count();
        $cancelled = PickupRequest::where('user_id', $userId)->cancelled()->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'confirmed' => $confirmed,
            'pickup_scheduled' => $pickupScheduled,
            'picked_up' => $pickedUp,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'failed' => $failed,
            'cancelled' => $cancelled,
        ];
    }

    public function getTotalRevenue(int $userId): array
    {
        $delivered = PickupRequest::where('user_id', $userId)
            ->delivered()
            ->get();

        $totalRevenue = $delivered->sum('product_total');
        $totalShippingCost = $delivered->sum('shipping_cost');
        $totalServiceFee = $delivered->sum('service_fee');
        $totalAmount = $delivered->sum('total_amount');

        return [
            'total_revenue' => $totalRevenue,
            'total_shipping_cost' => $totalShippingCost,
            'total_service_fee' => $totalServiceFee,
            'total_amount' => $totalAmount,
            'total_orders' => $delivered->count(),
        ];
    }

    public function getMonthlyStats(int $userId, int $year = null): array
    {
        $year = $year ?? now()->year;
        
        $monthlyData = PickupRequest::where('user_id', $userId)
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total_orders, SUM(product_total) as total_revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $monthlyData->firstWhere('month', $i);
            $result[] = [
                'month' => $i,
                'month_name' => now()->month($i)->format('F'),
                'total_orders' => $monthData->total_orders ?? 0,
                'total_revenue' => $monthData->total_revenue ?? 0,
            ];
        }

        return $result;
    }
}