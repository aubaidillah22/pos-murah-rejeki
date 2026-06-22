<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Product Search & List -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           wire:model.live="search" 
                           placeholder="Cari produk (nama, SKU, atau scan barcode)..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-gray-100"
                           autofocus>
                </div>

                @if(strlen($search) >= 1 && count($products) > 0)
                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-64 overflow-y-auto">
                    @foreach($products as $product)
                    <button wire:click="addToCart({{ $product['id'] }})" 
                            class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors text-left">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $product['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Stok: {{ $product['stock'] }} {{ $product['unit']['name'] ?? 'Unit' }}</p>
                        </div>
                        <span class="text-sm font-semibold text-emerald-600 ml-2">Rp {{ number_format($product['selling_price'], 0, ',', '.') }}</span>
                    </button>
                    @endforeach
                </div>
                @elseif(strlen($search) >= 1)
                <p class="text-sm text-gray-400 text-center py-4">Produk tidak ditemukan</p>
                @endif
            </div>

            <!-- Quick Product Categories -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-3">Kategori Cepat</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(\App\Models\Category::where('is_active', true)->get() as $category)
                    <button wire:click="$set('search', {{ json_encode($category->name) }})" 
                            class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-emerald-100 hover:text-emerald-700 transition-colors">
                        {{ $category->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Receipt Modal -->
            @if($showReceiptModal && $lastTransaction)
            @php
                $storeName = \App\Models\Setting::getValue('store_name', config('app.name'));
                $storeAddress = \App\Models\Setting::getValue('store_address', '');
                $storePhone = \App\Models\Setting::getValue('store_phone', '');
                $storeLogo = \App\Models\Setting::getValue('store_logo', '');
                $receiptFooter = \App\Models\Setting::getValue('receipt_footer', 'Terima kasih telah berbelanja di toko kami.');
            @endphp
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
                    <div id="receipt">
                        <div class="text-center mb-3 border-b border-gray-200 dark:border-gray-700 pb-3">
                            @if($storeLogo)
                            <img src="{{ Storage::url('settings/' . $storeLogo) }}" class="h-12 mx-auto mb-2">
                            @endif
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $storeName }}</h3>
                            @if($storeAddress)<p class="text-xs text-gray-500 dark:text-gray-400">{{ $storeAddress }}</p>@endif
                            @if($storePhone)<p class="text-xs text-gray-500 dark:text-gray-400">Telp: {{ $storePhone }}</p>@endif
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $lastTransaction->invoice_number }}</p>
                            <p class="text-xs text-gray-400">{{ $lastTransaction->transaction_date->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="text-center text-sm font-semibold text-emerald-600 mb-3">
                            ✓ Transaksi Berhasil
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-2 mb-3">
                            @foreach($lastTransaction->details as $detail)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-300">{{ $detail->product->name }} x{{ $detail->quantity }}</span>
                                <span class="font-medium">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 space-y-1">
                            @if($lastTransaction->discount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Diskon</span>
                                <span class="text-red-500">-Rp {{ number_format($lastTransaction->discount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if($lastTransaction->tax > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Pajak</span>
                                <span>Rp {{ number_format($lastTransaction->tax, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-base font-bold border-t border-gray-200 dark:border-gray-700 pt-1">
                                <span>Total</span>
                                <span class="text-emerald-600">Rp {{ number_format($lastTransaction->grand_total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Bayar</span>
                                <span>Rp {{ number_format($lastTransaction->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            @if($lastTransaction->change_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Kembali</span>
                                <span class="font-medium">Rp {{ number_format($lastTransaction->change_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if($lastTransaction->payment_method)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Metode</span>
                                <span>{{ ucfirst($lastTransaction->payment_method) }}</span>
                            </div>
                            @endif
                        </div>

                        @if($receiptFooter)
                        <div class="text-center text-xs text-gray-400 italic mt-4 pt-3 border-t border-dashed border-gray-200 dark:border-gray-700">
                            {{ $receiptFooter }}
                        </div>
                        @endif
                    </div>

                    <div class="flex gap-2 mt-4">
                        <button onclick="window.print()" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                            🖨️ Cetak Struk
                        </button>
                        <button wire:click="newTransaction" class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                            Transaksi Baru
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Cart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col" style="height: calc(100vh - 8rem);">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">🛒 Keranjang</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($cart) }} item</span>
            </div>

            <!-- Customer Selection -->
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Pelanggan:</span>
                    <button wire:click="$toggle('showCustomerModal')" class="text-sm text-emerald-600 hover:text-emerald-700">
                        {{ $customer_name ?: 'Pilih Pelanggan' }}
                    </button>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto space-y-2 mb-4">
                @forelse($cart as $index => $item)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Rp {{ number_format($item['selling_price'], 0, ',', '.') }}/{{ $item['unit'] }}</p>
                        </div>
                        <button wire:click="removeFromCart({{ $index }})" class="text-red-400 hover:text-red-600 ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center space-x-2">
                            <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})" 
                                    class="w-7 h-7 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-300 text-sm">-</button>
                            <input type="number" value="{{ $item['quantity'] }}" 
                                   wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                   class="w-14 text-center border border-gray-300 dark:border-gray-600 rounded-lg text-sm py-1 dark:bg-gray-700 dark:text-gray-100" min="1" max="{{ $item['stock'] }}">
                            <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})" 
                                    class="w-7 h-7 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-300 text-sm">+</button>
                        </div>
                        <span class="text-sm font-semibold text-emerald-600">Rp {{ number_format($item['sub_total'], 0, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <p class="text-gray-400">Keranjang kosong</p>
                    <p class="text-xs text-gray-300 mt-1">Cari produk untuk memulai transaksi</p>
                </div>
                @endforelse
            </div>

            <!-- Summary & Payment -->
            @if(count($cart) > 0)
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Total</span>
                    <span class="font-medium">Rp {{ number_format($cart_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Diskon</span>
                    <input type="number" wire:model.live="discount" class="w-28 text-right border border-gray-300 dark:border-gray-600 rounded text-sm py-1 dark:bg-gray-700 dark:text-gray-100" placeholder="0">
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Pajak (PPN)</span>
                    <input type="number" wire:model.live="tax" class="w-28 text-right border border-gray-300 dark:border-gray-600 rounded text-sm py-1 dark:bg-gray-700 dark:text-gray-100" placeholder="0">
                </div>
                <div class="flex justify-between text-base font-bold border-t border-gray-200 dark:border-gray-700 pt-2">
                    <span>Grand Total</span>
                    <span class="text-emerald-600">Rp {{ number_format($grand_total, 0, ',', '.') }}</span>
                </div>

                <button wire:click="openPaymentModal" 
                        class="w-full mt-3 px-4 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium text-sm">
                    Bayar (Rp {{ number_format($grand_total, 0, ',', '.') }})
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Customer Modal -->
    @if($showCustomerModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 relative">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Pilih Pelanggan</h3>
            
            <input type="text" wire:model.live="customer_search" 
                   placeholder="Cari nama atau telepon..."
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 mb-3">

            <div class="max-h-40 overflow-y-auto space-y-1 mb-4">
                @foreach($customers as $customer)
                <button wire:click="selectCustomer({{ $customer['id'] }})" 
                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    {{ $customer['name'] }} - {{ $customer['phone'] ?? '-' }}
                </button>
                @endforeach
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pelanggan Baru</h4>
                <input type="text" wire:model="customer_name" placeholder="Nama" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 mb-2">
                <input type="text" wire:model="customer_phone" placeholder="Telepon" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 mb-3">
                <button wire:click="createCustomer" class="w-full px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">
                    Tambah Pelanggan
                </button>
            </div>

            <button wire:click="$toggle('showCustomerModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">&times;</button>
        </div>
    </div>
    @endif

    <!-- Payment Modal -->
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Pembayaran</h3>
            
            <div class="space-y-3 mb-4">
                <div class="flex justify-between text-lg font-bold">
                    <span>Total Bayar</span>
                    <span class="text-emerald-600">Rp {{ number_format($grand_total, 0, ',', '.') }}</span>
                </div>

                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-300">Metode Pembayaran</label>
                    <select wire:model="payment_method" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 mt-1">
                        <option value="cash">Tunai</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer</option>
                        <option value="debit">Debit</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-300">Jumlah Dibayar</label>
                    <input type="number" wire:model.live="paid_amount" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 mt-1">
                </div>

                @if($paid_amount >= $grand_total)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Kembalian</span>
                    <span class="font-medium text-green-600">Rp {{ number_format($change_amount, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($paid_amount < $grand_total)
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-300">Status Pembayaran</label>
                    <select wire:model="payment_status" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 mt-1">
                        <option value="due">Piutang (Belum Lunas)</option>
                    </select>
                </div>
                @endif

                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-300">Catatan</label>
                    <textarea wire:model="notes" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 mt-1" rows="2"></textarea>
                </div>
            </div>

            <div class="flex gap-2">
                <button wire:click="$toggle('showPaymentModal')" class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">
                    Batal
                </button>
                <button wire:click="processPayment" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                    Proses Pembayaran
                </button>
            </div>
        </div>
    </div>
    @endif

    <style>
        @media print {
            body * { visibility: hidden; }
            #receipt, #receipt * { visibility: visible; }
            #receipt { position: absolute; left: 0; top: 0; width: 80mm; padding: 10px; }
        }
    </style>
</div>
