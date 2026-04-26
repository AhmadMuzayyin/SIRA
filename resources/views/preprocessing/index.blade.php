<x-layouts::app :title="__('Preprocessing Data')">
    <div class="space-y-5">
        @if (session('status'))
            <div
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <h1 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Preprocessing Otomatis</h1>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Hitung fitur ringkasan pelanggaran setiap santri
                (total, kategori, pola kehadiran/jamaah).</p>
            <form method="POST" action="{{ route('preprocessing.run') }}" class="mt-4">
                @csrf
                <button type="submit"
                    class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Jalankan
                    Preprocessing</button>
            </form>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 text-left text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:border-zinc-700 dark:text-zinc-300">
                            <th class="px-2 py-3">Santri</th>
                            <th class="px-2 py-3">Total</th>
                            <th class="px-2 py-3">Ringan</th>
                            <th class="px-2 py-3">Sedang</th>
                            <th class="px-2 py-3">Berat</th>
                            <th class="px-2 py-3">Indikasi Jamaah/Absensi</th>
                            <th class="px-2 py-3">Diproses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($preprocessings as $item)
                            <tr class="border-b border-zinc-100 text-zinc-800 dark:border-zinc-800 dark:text-zinc-200">
                                <td class="px-2 py-3">{{ $item->student?->nis }} - {{ $item->student?->name }}</td>
                                <td class="px-2 py-3">{{ $item->total_violations }}</td>
                                <td class="px-2 py-3">{{ $item->ringan_count }}</td>
                                <td class="px-2 py-3">{{ $item->sedang_count }}</td>
                                <td class="px-2 py-3">{{ $item->berat_count }}</td>
                                <td class="px-2 py-3">{{ $item->jamaah_absence_count }}</td>
                                <td class="px-2 py-3">{{ $item->processed_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-2 py-6 text-center text-zinc-500 dark:text-zinc-400">Belum
                                    ada data preprocessing.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $preprocessings->links() }}
            </div>
        </section>
    </div>
</x-layouts::app>
