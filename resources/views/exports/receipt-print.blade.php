<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Struk - {{ $transaction->invoice_number }}</title>
    @php
        $storeName = \App\Models\Setting::getValue('store_name', config('app.name'));
        $storeAddress = \App\Models\Setting::getValue('store_address', '');
        $storePhone = \App\Models\Setting::getValue('store_phone', '');
        $receiptFooter = \App\Models\Setting::getValue('receipt_footer', 'Terima kasih telah berbelanja di toko kami.');
        $receiptHeader = \App\Models\Setting::getValue('receipt_header', '');
        $receiptWidth = \App\Models\Setting::getValue('receipt_width', '80mm');
        $showAddress = \App\Models\Setting::getValue('receipt_show_address', '1') === '1';
        $showPhone = \App\Models\Setting::getValue('receipt_show_phone', '1') === '1';
        $showTax = \App\Models\Setting::getValue('receipt_show_tax', '1') === '1';
        $showDiscount = \App\Models\Setting::getValue('receipt_show_discount', '1') === '1';
        $showPaymentMethod = \App\Models\Setting::getValue('receipt_show_payment_method', '1') === '1';
        $showChange = \App\Models\Setting::getValue('receipt_show_change', '1') === '1';
        $showSku = \App\Models\Setting::getValue('receipt_show_sku', '0') === '1';
    @endphp
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; color: #000; width: {{ $receiptWidth }}; margin: 0 auto; padding: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .border-b { border-bottom: 1px solid #000; }
        .border-t { border-top: 1px solid #000; }
        .border-dashed { border-style: dashed; }
        .pt-1 { padding-top: 4px; }
        .pt-2 { padding-top: 8px; }
        .pt-3 { padding-top: 12px; }
        .pb-1 { padding-bottom: 4px; }
        .pb-2 { padding-bottom: 8px; }
        .pb-3 { padding-bottom: 12px; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
        .text-xs { font-size: 10px; }
        .text-sm { font-size: 12px; }
        .text-base { font-size: 14px; }
        .font-bold { font-weight: bold; }
        .font-semibold { font-weight: 600; }
        .italic { font-style: italic; }
        .text-gray-500 { color: #666; }
        .text-emerald-600 { color: #059669; }
        .text-red-500 { color: #ef4444; }
        .text-gray-400 { color: #999; }
        .space-y-1 > * + * { margin-top: 4px; }
        .space-y-2 > * + * { margin-top: 8px; }
        @media print {
            body { margin: 0; padding: 5px; }
            @page { margin: 0; }
        }
    </style>
</head>
<body>
    <div id="receipt">
        <div class="text-center mb-3 border-b pb-3">
            <div class="text-base font-bold">{{ $storeName }}</div>
            @if($storeAddress && $showAddress)<div class="text-xs text-gray-500">{{ $storeAddress }}</div>@endif
            @if($storePhone && $showPhone)<div class="text-xs text-gray-500">Telp: {{ $storePhone }}</div>@endif
            <div class="text-sm mt-1">{{ $transaction->invoice_number }}</div>
            <div class="text-xs text-gray-400">{{ $transaction->transaction_date->format('d/m/Y H:i') }}</div>
        </div>

        @if($receiptHeader)
        <div class="text-center text-xs italic mb-2">{{ $receiptHeader }}</div>
        @endif

        @if($transaction->isVoided())
        <div class="text-center text-sm font-bold" style="color: #dc2626; margin-bottom: 8px;">
            ~~~ VOID / BATAL ~~~
        </div>
        @endif

        <div class="border-t pt-3 space-y-2 mb-3">
            @foreach($transaction->details as $detail)
            <div class="flex justify-between text-sm">
                <span>
                    @if($showSku && $detail->product && $detail->product->sku)
                        [{{ $detail->product->sku }}]
                    @endif
                    {{ $detail->product?->name ?? 'Produk Dihapus' }} x{{ $detail->quantity }}
                </span>
                <span class="font-semibold">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <div class="border-t pt-2 space-y-1">
            @if($transaction->discount > 0 && $showDiscount)
            <div class="flex justify-between text-sm">
                <span>Diskon</span>
                <span class="text-red-500">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($transaction->tax > 0 && $showTax)
            <div class="flex justify-between text-sm">
                <span>Pajak</span>
                <span>Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-base font-bold border-t pt-1">
                <span>Total</span>
                <span class="text-emerald-600">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Bayar</span>
                <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            @if($transaction->change_amount > 0 && $showChange)
            <div class="flex justify-between text-sm">
                <span>Kembali</span>
                <span class="font-semibold">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($transaction->payment_method && $showPaymentMethod)
            <div class="flex justify-between text-sm">
                <span>Metode</span>
                <span>{{ ucfirst($transaction->payment_method) }}</span>
            </div>
            @endif
        </div>

        @if($transaction->customer)
        <div class="border-t pt-2 mt-2 text-xs">
            <div class="flex justify-between">
                <span>Pelanggan:</span>
                <span>{{ $transaction->customer->name }}</span>
            </div>
        </div>
        @endif

        @if($transaction->notes)
        <div class="border-t pt-2 mt-2 text-xs text-gray-500">
            <div>Catatan: {{ $transaction->notes }}</div>
        </div>
        @endif

        @if($receiptFooter)
        <div class="text-center text-xs text-gray-400 italic mt-4 pt-3 border-t border-dashed">
            {{ $receiptFooter }}
        </div>
        @endif
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() { window.close(); }, 500);
        };
    </script>
</body>
</html>
