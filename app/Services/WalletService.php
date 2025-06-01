<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getUserWallet(int $userId): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'pending_balance' => 0]
        );
    }

    public function getWalletTransactions(int $userId, int $limit = 50): Collection
    {
        $wallet = $this->getUserWallet($userId);
        return $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function addBalance(int $userId, float $amount, string $description, string $type = 'topup', ?int $referenceId = null): WalletTransaction
    {
        return DB::transaction(function () use ($userId, $amount, $description, $type, $referenceId) {
            $wallet = $this->getUserWallet($userId);
            return $wallet->addBalance($amount, $description, $type, $referenceId);
        });
    }

    public function deductBalance(int $userId, float $amount, string $description, string $type = 'payment', ?int $referenceId = null): WalletTransaction
    {
        return DB::transaction(function () use ($userId, $amount, $description, $type, $referenceId) {
            $wallet = $this->getUserWallet($userId);
            return $wallet->deductBalance($amount, $description, $type, $referenceId);
        });
    }

    public function getBalance(int $userId): array
    {
        $wallet = $this->getUserWallet($userId);
        return [
            'balance' => $wallet->balance,
            'pending_balance' => $wallet->pending_balance,
            'available_balance' => $wallet->available_balance
        ];
    }

    public function hasBalance(int $userId, float $amount): bool
    {
        $wallet = $this->getUserWallet($userId);
        return $wallet->hasSufficientBalance($amount);
    }

    public function transferBalance(int $fromUserId, int $toUserId, float $amount, string $description): array
    {
        return DB::transaction(function () use ($fromUserId, $toUserId, $amount, $description) {
            $fromWallet = $this->getUserWallet($fromUserId);
            $toWallet = $this->getUserWallet($toUserId);

            if (!$fromWallet->hasSufficientBalance($amount)) {
                throw new \Exception('Saldo tidak mencukupi');
            }

            $deductTransaction = $fromWallet->deductBalance($amount, $description, 'transfer_out', $toUserId);
            $addTransaction = $toWallet->addBalance($amount, $description, 'transfer_in', $fromUserId);

            return [
                'from_transaction' => $deductTransaction,
                'to_transaction' => $addTransaction
            ];
        });
    }

    public function setPendingBalance(int $userId, float $amount): Wallet
    {
        $wallet = $this->getUserWallet($userId);
        $wallet->update(['pending_balance' => $amount]);
        return $wallet;
    }

    public function addPendingBalance(int $userId, float $amount): Wallet
    {
        $wallet = $this->getUserWallet($userId);
        $wallet->increment('pending_balance', $amount);
        return $wallet->fresh();
    }

    public function deductPendingBalance(int $userId, float $amount): Wallet
    {
        $wallet = $this->getUserWallet($userId);
        $wallet->decrement('pending_balance', $amount);
        return $wallet->fresh();
    }

    public function getTransactionStats(int $userId): array
    {
        $wallet = $this->getUserWallet($userId);

        $totalTopup = $wallet->transactions()->topups()->success()->sum('amount');
        $totalWithdraw = $wallet->transactions()->withdrawals()->success()->sum('amount');
        $totalPayment = $wallet->transactions()->payments()->success()->sum('amount');
        $totalRefund = $wallet->transactions()->refunds()->success()->sum('amount');

        return [
            'total_topup' => $totalTopup,
            'total_withdraw' => $totalWithdraw,
            'total_payment' => $totalPayment,
            'total_refund' => $totalRefund,
            'current_balance' => $wallet->balance,
            'available_balance' => $wallet->available_balance
        ];
    }

    public function getTopTransactions(int $userId, string $type, int $limit = 10): Collection
    {
        $wallet = $this->getUserWallet($userId);
        return $wallet->transactions()
            ->where('type', $type)
            ->orderBy('amount', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMonthlyTransactionSummary(int $userId, int $year, int $month): array
    {
        $wallet = $this->getUserWallet($userId);

        $transactions = $wallet->transactions()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $summary = [
            'topup' => 0,
            'withdraw' => 0,
            'payment' => 0,
            'refund' => 0,
            'transfer_in' => 0,
            'transfer_out' => 0
        ];

        foreach ($transactions as $transaction) {
            if (isset($summary[$transaction->type])) {
                $summary[$transaction->type] += $transaction->amount;
            }
        }

        return $summary;
    }
}
