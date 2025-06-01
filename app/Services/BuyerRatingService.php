<?php

namespace App\Services;

use App\Models\BuyerRating;
use Illuminate\Database\Eloquent\Collection;

class BuyerRatingService
{
    public function getAllRatings(): Collection
    {
        return BuyerRating::orderBy('success_rate', 'asc')->get();
    }

    public function getRatingByPhone(string $phoneNumber): ?BuyerRating
    {
        return BuyerRating::byPhone($phoneNumber)->first();
    }

    public function getHighRiskBuyers(): Collection
    {
        return BuyerRating::highRisk()->orderBy('success_rate', 'asc')->get();
    }

    public function createOrUpdateRating(array $data): BuyerRating
    {
        $rating = BuyerRating::byPhone($data['phone_number'])->first();

        if ($rating) {
            $rating->update($data);
        } else {
            $rating = BuyerRating::create($data);
        }

        return $rating;
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
                'risk_level' => 'low'
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
            return 'low';
        } elseif ($successRate >= 60) {
            return 'medium';
        } else {
            return 'high';
        }
    }

    public function deleteRating(int $id): bool
    {
        $rating = BuyerRating::find($id);
        return $rating ? $rating->delete() : false;
    }

    public function getRatingStats(): array
    {
        $total = BuyerRating::count();
        $highRisk = BuyerRating::highRisk()->count();
        $mediumRisk = BuyerRating::where('risk_level', 'medium')->count();
        $lowRisk = BuyerRating::where('risk_level', 'low')->count();

        return [
            'total' => $total,
            'high_risk' => $highRisk,
            'medium_risk' => $mediumRisk,
            'low_risk' => $lowRisk,
            'high_risk_percentage' => $total > 0 ? round(($highRisk / $total) * 100, 2) : 0
        ];
    }
}
