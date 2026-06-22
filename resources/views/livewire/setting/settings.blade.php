<div>
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="flex gap-6">
            <button wire:click="$set('activeTab', 'general')"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors
                           {{ $activeTab === 'general' ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                ⚙️ Umum
            </button>
            <button wire:click="$set('activeTab', 'logo')"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors
                           {{ $activeTab === 'logo' ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                🖼️ Logo Toko
            </button>
            <button wire:click="$set('activeTab', 'receipt')"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors
                           {{ $activeTab === 'receipt' ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                🧾 Struk Transaksi
            </button>
        </nav>
    </div>

    <form wire:submit="save">
        <!-- General Settings -->
        @if($activeTab === 'general')
        <div class="card p-6 space-y-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Pengaturan Umum</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Toko</label>
                    <input type="text" wire:model="store_name" placeholder="Murah Rejeki"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('store_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pajak Default (PPN %)</label>
                    <input type="number" wire:model="default_tax" step="0.1" min="0" max="100"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('default_tax') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-400 mt-1">PPN default untuk transaksi POS</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telepon Toko</label>
                    <input type="text" wire:model="store_phone" placeholder="021-12345678"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('store_phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Toko</label>
                <textarea wire:model="store_address" rows="3" placeholder="Jl. Raya Utama No. 123, Jakarta"
                          class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                @error('store_address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-2">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Informasi Sistem</h4>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Mata Uang:</span>
                        <span class="ml-1 font-medium dark:text-gray-200">IDR (Rupiah)</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Timezone:</span>
                        <span class="ml-1 font-medium dark:text-gray-200">Asia/Jakarta (WIB)</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Laravel:</span>
                        <span class="ml-1 font-medium dark:text-gray-200">{{ app()->version() }}</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                    💾 Simpan Pengaturan
                </button>
            </div>
        </div>
        @endif

        <!-- Logo Settings -->
        @if($activeTab === 'logo')
        <div class="card p-6 space-y-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Logo Toko</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Upload logo toko untuk ditampilkan di sidebar dan struk transaksi. Format PNG/JPG, maks 2MB.</p>

            <div class="flex items-center gap-6">
                <div class="w-32 h-32 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-700">
                    @if($temp_logo)
                        <img src="{{ $temp_logo->temporaryUrl() }}" class="w-full h-full object-contain p-2">
                    @elseif($store_logo)
                        <img src="{{ Storage::disk('public')->url('settings/' . $store_logo) }}" class="w-full h-full object-contain p-2">
                    @else
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    @endif
                </div>
                <div class="space-y-3">
                    <label class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Pilih Gambar
                        <input type="file" wire:model="temp_logo" accept="image/png,image/jpg,image/jpeg" class="hidden">
                    </label>
                    @error('temp_logo') <span class="text-xs text-red-500 block">{{ $message }}</span> @enderror

                    @if($store_logo || $temp_logo)
                    <label class="flex items-center gap-2 text-sm text-red-600 hover:text-red-700 cursor-pointer">
                        <input type="checkbox" wire:model="remove_logo" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        Hapus logo
                    </label>
                    @endif
                </div>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                    💾 Simpan Logo
                </button>
            </div>
        </div>
        @endif

        <!-- Receipt Settings -->
        @if($activeTab === 'receipt')
        <div class="card p-6 space-y-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Kustomisasi Struk Transaksi</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Sesuaikan tampilan struk yang dicetak setelah transaksi POS.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ukuran Struk</label>
                    <select wire:model="receipt_width" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="80mm">80mm (Thermal Lebar)</option>
                        <option value="58mm">58mm (Thermal Sempit)</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Pilih ukuran kertas struk sesuai printer thermal</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pesan Kepala Struk</label>
                    <textarea wire:model="receipt_header" rows="2" maxlength="500"
                              placeholder="Terima kasih sudah berbelanja..."
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    @error('receipt_header') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-400 mt-1">Pesan tambahan di bagian atas struk</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pesan Kaki Struk</label>
                <textarea wire:model="receipt_footer" rows="2" maxlength="500"
                          placeholder="Terima kasih telah berbelanja di toko kami."
                          class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                @error('receipt_footer') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-400 mt-1">Pesan di bagian bawah struk. Maksimal 500 karakter.</p>
            </div>

            <!-- Tampilkan / Sembunyikan Elemen -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">👁️ Tampilkan / Sembunyikan Elemen</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" wire:model="receipt_show_logo" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        Logo Toko
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" wire:model="receipt_show_address" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        Alamat Toko
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" wire:model="receipt_show_phone" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        Telepon Toko
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" wire:model="receipt_show_tax" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        Detail Pajak
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" wire:model="receipt_show_discount" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        Detail Diskon
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" wire:model="receipt_show_payment_method" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        Metode Bayar
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" wire:model="receipt_show_change" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        Kembalian
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" wire:model="receipt_show_sku" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        SKU Produk
                    </label>
                </div>
            </div>

            <!-- Receipt Preview -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 overflow-x-auto">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">📋 Pratinjau Struk</h4>
                <div id="receipt-preview" class="bg-white dark:bg-gray-800 rounded-lg mx-auto p-3 sm:p-4 text-xs" style="max-width: min(100%, {{ $receipt_width === '58mm' ? '58mm' : '80mm' }}); width: {{ $receipt_width === '58mm' ? '58mm' : '80mm' }};">
                    <div class="text-center border-b border-gray-300 dark:border-gray-600 pb-2 mb-2">
                        @if($store_logo && $receipt_show_logo)
                            <img src="{{ Storage::disk('public')->url('settings/' . $store_logo) }}" class="h-10 mx-auto mb-1">
                        @endif
                        <div class="font-bold text-sm">{{ $store_name ?: 'Nama Toko' }}</div>
                        @if($store_address && $receipt_show_address)
                            <div class="text-gray-500">{{ $store_address }}</div>
                        @endif
                        @if($store_phone && $receipt_show_phone)
                            <div class="text-gray-500">Telp: {{ $store_phone }}</div>
                        @endif
                    </div>
                    @if($receipt_header)
                    <div class="text-center text-gray-500 italic mb-2">{{ $receipt_header }}</div>
                    @endif
                    <div class="text-center text-gray-400 mb-2">#INV-20260623-0001</div>
                    <div class="border-b border-dashed border-gray-300 dark:border-gray-600 pb-1 mb-1">
                        <div class="flex justify-between"><span>Produk A x2</span><span>Rp 20.000</span></div>
                        <div class="flex justify-between"><span>Produk B x1</span><span>Rp 15.000</span></div>
                    </div>
                    @if($receipt_show_discount)
                    <div class="flex justify-between text-gray-400">
                        <span>Diskon</span><span>-Rp 5.000</span>
                    </div>
                    @endif
                    @if($receipt_show_tax)
                    <div class="flex justify-between text-gray-400">
                        <span>Pajak (11%)</span><span>Rp 3.300</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold border-b border-gray-300 dark:border-gray-600 pb-2 mb-2">
                        <span>Total</span><span>Rp 33.300</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Bayar</span><span>Rp 50.000</span>
                    </div>
                    @if($receipt_show_change)
                    <div class="flex justify-between text-gray-500">
                        <span>Kembali</span><span>Rp 16.700</span>
                    </div>
                    @endif
                    @if($receipt_show_payment_method)
                    <div class="flex justify-between text-gray-500">
                        <span>Metode</span><span>Tunai</span>
                    </div>
                    @endif
                    <div class="text-center text-gray-400 text-xs mt-1">{{ now()->format('d/m/Y H:i') }}</div>
                    @if($receipt_footer)
                    <div class="text-center text-gray-500 italic pt-1 mt-1 border-t border-dashed border-gray-300 dark:border-gray-600">
                        {{ $receipt_footer }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                    💾 Simpan Pengaturan Struk
                </button>
            </div>
        </div>
        @endif
    </form>

    @push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            Livewire.on('settings-saved', () => {
                setTimeout(() => location.reload(), 100);
            });
        });
    </script>
    @endpush
</div>
