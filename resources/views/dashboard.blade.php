<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-4">
        @if (session('status'))
            <div
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-3 md:grid-cols-5">
            <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Total Santri</p>
                <p class="mt-1 text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['students'] }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Kriteria Pelanggaran</p>
                <p class="mt-1 text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['criteria'] }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Master Pelanggaran</p>
                <p class="mt-1 text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['violations'] }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Pelanggaran Tercatat</p>
                <p class="mt-1 text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['student_violations'] }}
                </p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Risiko Tinggi</p>
                <p class="mt-1 text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['high_risk'] }}
                </p>
            </div>
        </div>

        <div class="grid gap-3 xl:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Komposisi Prediksi Risiko</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Perbandingan santri yang diprediksi aman dan
                    berisiko tinggi.</p>
                <div class="mt-6 flex items-center justify-center">
                    <div class="relative h-44 w-44 rounded-full"
                        style="background: conic-gradient(rgb(16 185 129) {{ $safePercent }}%, rgb(239 68 68) {{ $safePercent }}%);">
                        <div class="absolute inset-5 rounded-full bg-white dark:bg-zinc-900"></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">High Risk</p>
                            <p class="text-3xl font-bold text-red-500">{{ $riskPercent }}%</p>
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1"><span
                            class="size-3 rounded-sm bg-emerald-500"></span>Aman</span>
                    <span class="inline-flex items-center gap-1"><span
                            class="size-3 rounded-sm bg-red-500"></span>Risiko Tinggi</span>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Top 5 Risiko Tertinggi</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Santri dengan probabilitas risiko paling tinggi saat
                    ini.</p>
                <div class="mt-5 flex h-60 items-end gap-4 overflow-x-auto">
                    @php
                        $topFive = $topRankings->take(5);
                    @endphp
                    @forelse ($topFive as $item)
                        @php
                            $barPercent = min(100, max(5, (float) $item->risk_probability * 100));
                        @endphp
                        <div class="flex min-w-28 flex-1 flex-col items-center gap-2">
                            <div class="w-full rounded-t-lg bg-violet-600/90" style="height: {{ $barPercent * 2 }}px">
                            </div>
                            <p class="line-clamp-1 text-center text-xs text-zinc-600 dark:text-zinc-300">
                                {{ $item->student?->name }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Belum ada data prediksi.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Top Ranking Risiko</h2>
                <a href="{{ route('predictions.index') }}" wire:navigate
                    class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Lihat
                    Detail</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 text-left text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                            <th class="px-2 py-2">Rank</th>
                            <th class="px-2 py-2">NIS</th>
                            <th class="px-2 py-2">Nama</th>
                            <th class="px-2 py-2">Probabilitas</th>
                            <th class="px-2 py-2">Prediksi</th>
                            <th class="px-2 py-2">Potensi Pelanggaran Lain</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topRankings as $prediction)
                            <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                <td class="px-2 py-2">{{ $prediction->rank_score }}</td>
                                <td class="px-2 py-2">{{ $prediction->student?->nis }}</td>
                                <td class="px-2 py-2">{{ $prediction->student?->name }}</td>
                                <td class="px-2 py-2">
                                    {{ number_format((float) $prediction->risk_probability * 100, 2) }}%</td>
                                <td class="px-2 py-2">
                                    @if ($prediction->predicted_to_reoffend)
                                        <span
                                            class="rounded-lg bg-red-100 px-2 py-1 text-xs text-red-700 dark:bg-red-900/40 dark:text-red-200">Berpotensi
                                            Melanggar Lagi</span>
                                    @else
                                        <span
                                            class="rounded-lg bg-emerald-100 px-2 py-1 text-xs text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">Relatif
                                            Aman</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">{{ $prediction->suggestedViolation?->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-2 py-6 text-center text-zinc-500 dark:text-zinc-400">Belum
                                    ada data ranking.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts::app>
