<?php

namespace App\Services;

use App\Models\CashFlow;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class POSService
{
    public function processTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Calculate totals
            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subTotal = $product->selling_price * $item['quantity'];
                $itemDiscount = $item['discount'] ?? 0;
                $totalAmount += $subTotal - $itemDiscount;
            }

            $discount = $data['discount'] ?? 0;
            $tax = $data['tax'] ?? 0;
            $grandTotal = $totalAmount - $discount + $tax;

            // Create transaction
            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $data['customer_id'] ?? null,
                'outlet_id' => $data['outlet_id'],
                'user_id' => $data['user_id'],
                'transaction_date' => now(),
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'paid_amount' => $data['paid_amount'] ?? $grandTotal,
                'change_amount' => max(0, ($data['paid_amount'] ?? $grandTotal) - $grandTotal),
                'payment_method' => $data['payment_method'] ?? 'cash',
                'payment_status' => $data['payment_status'] ?? 'paid',
                'due_date' => $data['due_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create transaction details and reduce stock
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subTotal = $product->selling_price * $item['quantity'];
                $itemDiscount = $item['discount'] ?? 0;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'selling_price' => $product->selling_price,
                    'discount' => $itemDiscount,
                    'sub_total' => $subTotal - $itemDiscount,
                ]);

                // Reduce stock
                $product->decrement('stock', $item['quantity']);

                // Log low stock
                if ($product->isStockLow()) {
                    Log::warning("Stok menipis: {$product->name} (SKU: {$product->sku})");
                }
            }

            // Record cash flow
            if ($data['payment_status'] === 'paid') {
                CashFlow::create([
                    'transaction_type' => 'income',
                    'reference_type' => Transaction::class,
                    'reference_id' => $transaction->id,
                    'amount' => $grandTotal,
                    'description' => "Penjualan {$transaction->invoice_number}",
                ]);
            }

            // Log activity
            activity()
                ->performedOn($transaction)
                ->causedBy($data['user_id'])
                ->log("Transaksi POS: {$invoiceNumber} - Rp " . number_format($grandTotal, 0, ',', '.'));

            return $transaction->load(['details.product', 'customer', 'user']);
        });
    }

    public function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $lastTransaction = Transaction::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTransaction ? $lastTransaction->id + 1 : 1;
        return 'INV-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function voidTransaction(int $transactionId, string $reason): Transaction
    {
        return DB::transaction(function () use ($transactionId, $reason) {
            $transaction = Transaction::with('details.product')->findOrFail($transactionId);

            if ($transaction->voided_at) {
                throw new \Exception('Transaksi sudah di-void sebelumnya.');
            }

            // Restore stock for all products
            foreach ($transaction->details as $detail) {
                if ($detail->product) {
                    $detail->product->increment('stock', $detail->quantity);
                }
            }

            // Record void cash flow (refund)
            CashFlow::create([
                'transaction_type' => 'expense',
                'reference_type' => Transaction::class,
                'reference_id' => $transaction->id,
                'amount' => $transaction->grand_total,
                'description' => "Void: {$transaction->invoice_number} - {$reason}",
            ]);

            // Mark transaction as voided
            $transaction->update([
                'voided_at' => now(),
                'void_reason' => $reason,
                'voided_by' => auth()->id(),
            ]);

            // Log activity
            activity()
                ->performedOn($transaction)
                ->log("Transaksi di-Void: {$transaction->invoice_number} - Alasan: {$reason}");

            return $transaction->fresh()->load(['details.product', 'customer', 'user']);
        });
    }

    public function processPaymentDue(int $transactionId, float $amount): void
    {
        DB::transaction(function () use ($transactionId, $amount) {
            $transaction = Transaction::findOrFail($transactionId);
            
            $transaction->update([
                'payment_status' => 'paid',
                'paid_amount' => $amount,
            ]);

            CashFlow::create([
                'transaction_type' => 'income',
                'reference_type' => Transaction::class,
                'reference_id' => $transaction->id,
                'amount' => $amount,
                'description' => "Pelunasan {$transaction->invoice_number}",
            ]);
        });
    }
}
