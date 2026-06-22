<div>
    <form wire:submit="save">
        <div class="card p-6 max-w-2xl space-y-6">
            <h3 class="text-lg font-semibold text-gray-800">Pengaturan Toko</h3>

            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko</label>
                    <input type="text" value="{{ config('app.name') }}" readonly 
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 text-sm" disabled>
                    <p class="text-xs text-gray-400 mt-1">Nama toko diambil dari konfigurasi APP_NAME di .env</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pajak Default (PPN %)</label>
                        <input type="number" wire:model="default_tax" step="0.1" min="0" max="100"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        @error('default_tax') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-400 mt-1">PPN default untuk transaksi POS</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon Toko</label>
                        <input type="text" wire:model="store_phone" placeholder="021-12345678"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        @error('store_phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Toko</label>
                    <textarea wire:model="store_address" rows="3" placeholder="Jl. Raya Utama No. 123, Jakarta"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"></textarea>
                    @error('store_address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <h4 class="text-sm font-medium text-gray-700">Informasi Sistem</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-gray-500">Mata Uang:</span>
                            <span class="ml-1 font-medium">IDR (Rupiah)</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Timezone:</span>
                            <span class="ml-1 font-medium">Asia/Jakarta (WIB)</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Versi App:</span>
                            <span class="ml-1 font-medium">{{ config('app.version', '1.0') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Laravel:</span>
                            <span class="ml-1 font-medium">{{ app()->version() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-200">
                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                    💾 Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</div>
