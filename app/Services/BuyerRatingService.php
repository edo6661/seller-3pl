<?php
namespace App\Services;

use App\Models\BuyerRating;
use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BuyerRatingService
{
    public function getAllRatings(): Collection
    {
        return BuyerRating::orderBy('success_rate', 'asc')->get();
    }

    public function getPaginatedRatings(int $perPage = 10): LengthAwarePaginator
    {
        return BuyerRating::orderBy('success_rate', 'asc')->paginate($perPage);
    }

    public function searchRatings(string $search, int $perPage = 10): LengthAwarePaginator
    {
        return BuyerRating::where('name', 'like', "%{$search}%")
            ->orWhere('phone_number', 'like', "%{$search}%")
            ->orderBy('success_rate', 'asc')
            ->paginate($perPage);
    }

    public function getRatingById(int $id): ?BuyerRating
    {
        return BuyerRating::find($id);
    }

    public function getRatingByPhone(string $phoneNumber): ?BuyerRating
    {
        return BuyerRating::byPhone($phoneNumber)->first();
    }

    public function getHighRiskBuyers(): Collection
    {
        return BuyerRating::highRisk()->orderBy('success_rate', 'asc')->get();
    }

    public function createRating(array $data): BuyerRating
    {
        // Calculate success rate
        if (isset($data['total_orders']) && $data['total_orders'] > 0) {
            $data['success_rate'] = ($data['successful_orders'] / $data['total_orders']) * 100;
        }

        // Calculate risk level
        $data['risk_level'] = $this->calculateRiskLevel($data['success_rate'] ?? 0);

        return BuyerRating::create($data);
    }

    public function updateRating(int $id, array $data): ?BuyerRating
    {
        $rating = $this->getRatingById($id);
        
        if (!$rating) {
            return null;
        }

        // Recalculate success rate if order data changed
        if (isset($data['total_orders']) && $data['total_orders'] > 0) {
            $successfulOrders = $data['successful_orders'] ?? $rating->successful_orders;
            $data['success_rate'] = ($successfulOrders / $data['total_orders']) * 100;
        }

        // Recalculate risk level
        if (isset($data['success_rate'])) {
            $data['risk_level'] = $this->calculateRiskLevel($data['success_rate']);
        }

        $rating->update($data);
        return $rating;
    }

    public function deleteRating(int $id): bool
    {
        $rating = BuyerRating::find($id);
        return $rating ? $rating->delete() : false;
    }

    public function createOrUpdateRating(array $data): BuyerRating
    {
        $rating = BuyerRating::byPhone($data['phone_number'])->first();
        
        if ($rating) {
            // Update existing rating
            unset($data['phone_number']); // Don't update phone number
            $this->updateRating($rating->id, $data);
            return $rating->refresh();
        } else {
            // Create new rating
            return $this->createRating($data);
        }
    }

    public function updateOrderStats(string $phoneNumber, string $orderStatus): BuyerRating
    {
        $rating = $this->getRatingByPhone($phoneNumber);
        
        if (!$rating) {
            $rating = BuyerRating::create([
                'phone_number' => $phoneNumber,
                'name' => 'Unknown',
                'total_orders' => 0,
                'successful_orders' => 0,
                'failed_cod_orders' => 0,
                'cancelled_orders' => 0,
                'success_rate' => 0.00,
                'risk_level' => RiskLevel::LOW->value
            ]);
        }

        $rating->increment('total_orders');

        switch ($orderStatus) {
            case 'delivered':
                $rating->increment('successful_orders');
                break;
            case 'failed_cod':
                $rating->increment('failed_cod_orders');
                break;
            case 'cancelled':
                $rating->increment('cancelled_orders');
                break;
        }

        // Update success rate
        $rating->success_rate = $rating->total_orders > 0
            ? ($rating->successful_orders / $rating->total_orders) * 100
            : 0;

        // Update risk level
        $rating->risk_level = $this->calculateRiskLevel($rating->success_rate);
        $rating->save();

        return $rating;
    }

    public function calculateRiskLevel(float $successRate): string
    {
        if ($successRate >= 80) {
            return RiskLevel::LOW->value;
        } elseif ($successRate >= 60) {
            return RiskLevel::MEDIUM->value;
        } else {
            return RiskLevel::HIGH->value;
        }
    }

    public function getRatingStats(): array
    {
        $total = BuyerRating::count();
        $highRisk = BuyerRating::highRisk()->count();
        $mediumRisk = BuyerRating::where('risk_level', RiskLevel::MEDIUM->value)->count();
        $lowRisk = BuyerRating::where('risk_level', RiskLevel::LOW->value)->count();

        return [
            'total' => $total,
            'high_risk' => $highRisk,
            'medium_risk' => $mediumRisk,
            'low_risk' => $lowRisk,
            'high_risk_percentage' => $total > 0 ? round(($highRisk / $total) * 100, 2) : 0,
            'average_success_rate' => BuyerRating::avg('success_rate') ?? 0
        ];
    }
}
