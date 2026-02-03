<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckExpiredTransactions extends Command
{
    protected $signature = 'transactions:check-expired';
    protected $description = 'Check and update expired pending transactions after 24 hours';

    public function handle()
    {
        $this->info('Checking for expired transactions...');

        // Ambil transaksi yang pending dan sudah lebih dari 24 jam sejak dibuat
        $expiredTransactions = Transaction::with('details')
            ->where('payment_status', 'pending')
            ->where('created_at', '<=', Carbon::now()->subMinutes(1))
            ->get();

        $count = 0;

        foreach ($expiredTransactions as $transaction) {
            DB::beginTransaction();
            try {
                // 1. Kembalikan barang ke keranjang
                foreach ($transaction->details as $detail) {
                    $existingCart = Cart::where('user_id', $transaction->customer_id)
                        ->where('shoes_variant_id', $detail->variant_id)
                        ->first();

                    if ($existingCart) {
                        $existingCart->increment('quantity', $detail->qty);
                    } else {
                        Cart::create([
                            'user_id' => $transaction->customer_id,
                            'shoes_variant_id' => $detail->variant_id,
                            'quantity' => $detail->qty,
                        ]);
                    }
                }

                // 2. Update status transaksi
                $transaction->update([
                    'payment_status' => 'expire',
                    'transaction_status' => 'failed',
                    'expired_at' => now(),
                ]);

                DB::commit();
                $count++;

                $this->line("Transaction {$transaction->invoice} marked as expired.");
                Log::info("Transaction {$transaction->invoice} expired. Items returned to cart for user {$transaction->customer_id}.");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to process transaction {$transaction->invoice}: " . $e->getMessage());
                Log::error("Failed to expire transaction {$transaction->invoice}: " . $e->getMessage());
            }
        }

        $this->info("Completed. {$count} transactions marked as expired.");

        return Command::SUCCESS;
    }
}
