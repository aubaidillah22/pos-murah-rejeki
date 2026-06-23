<div>
    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl text-sm mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3 mb-4">
        <div class="flex items-start gap-2.5 text-sm text-amber-800 dark:text-amber-300">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <strong class="font-semibold">Fitur Pengembangan</strong>
                <p class="mt-0.5 text-amber-700 dark:text-amber-400">Fitur hapus pengeluaran, pengeluaran berulang, dan lampiran bukti masih dalam pengembangan dan akan segera hadir di update berikutnya.</p>
            </div>
        </div>
    </div>

    <div class="p-12 text-center">
        <p class="text-gray-400 dark:text-gray-500">Halaman ini akan tersedia pada update berikutnya.</p>
    </div>
</div>
