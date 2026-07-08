<x-layouts::app :title="__('Laporan')">
    <div class="space-y-5">
        <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Export Laporan</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                <a href="{{ route('students.export') }}"
                    class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Export
                    Data Santri (Excel)</a>
                <a href="{{ route('student-violations.export') }}"
                    class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Export
                    Pelanggaran Santri (Excel)</a>
                <a href="{{ route('reports.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Export
                    Prediksi & Ranking (Excel)</a>
                <a href="{{ route('reports.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Export
                    Prediksi & Ranking (PDF)</a>
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Preview Laporan Prediksi</h2>
                <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}"
                        class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800" />
                    <span class="text-zinc-500 dark:text-zinc-400">-</span>
                    <input type="date" name="end_date" value="{{ $endDate ?? '' }}"
                        class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800" />
                    <button type="submit"
                        class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Filter</button>
                </form>
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 text-left text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:border-zinc-700 dark:text-zinc-300">
                            <th class="px-2 py-3">Rank</th>
                            <th class="px-2 py-3">Santri</th>
                            <th class="px-2 py-3">Probabilitas</th>
                            <th class="px-2 py-3">Prediksi</th>
                            <th class="px-2 py-3">Potensi Pelanggaran Lain</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topRisk as $item)
                            <tr class="border-b border-zinc-100 text-zinc-800 dark:border-zinc-800 dark:text-zinc-200">
                                <td class="px-2 py-3">{{ $item->rank_score }}</td>
                                <td class="px-2 py-3">{{ $item->student?->nis }} - {{ $item->student?->name }}</td>
                                <td class="px-2 py-3">{{ number_format((float) $item->risk_probability * 100, 2) }}%
                                </td>
                                <td class="px-2 py-3">
                                    {{ $item->predicted_to_reoffend ? 'Berpotensi Melanggar Lagi' : 'Relatif Aman' }}
                                </td>
                                <td class="px-2 py-3">{{ $item->suggestedViolation?->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-2 py-6 text-center text-zinc-500 dark:text-zinc-400">Belum
                                    ada data prediksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts::app>
