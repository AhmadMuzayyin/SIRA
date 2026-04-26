<x-layouts::app :title="__('Prediksi & Ranking')">
    <div class="space-y-5">
        @if (session('status'))
            <div
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <h1 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Prediksi Naive Bayes Otomatis</h1>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Model memprediksi potensi santri melanggar lagi atau
                tidak, lalu memberi ranking risiko dan rekomendasi potensi pelanggaran lain.</p>
            <form method="POST" action="{{ route('predictions.run') }}" class="mt-4">
                @csrf
                <button type="submit"
                    class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Jalankan
                    Prediksi & Ranking</button>
            </form>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 text-left text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:border-zinc-700 dark:text-zinc-300">
                            <th class="px-2 py-3">Rank</th>
                            <th class="px-2 py-3">Santri</th>
                            <th class="px-2 py-3">Probabilitas Risiko</th>
                            <th class="px-2 py-3">Prediksi</th>
                            <th class="px-2 py-3">Potensi Pelanggaran Lain</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($predictions as $prediction)
                            <tr class="border-b border-zinc-100 text-zinc-800 dark:border-zinc-800 dark:text-zinc-200">
                                <td class="px-2 py-3">{{ $prediction->rank_score }}</td>
                                <td class="px-2 py-3">{{ $prediction->student?->nis }} -
                                    {{ $prediction->student?->name }}</td>
                                <td class="px-2 py-3">
                                    {{ number_format((float) $prediction->risk_probability * 100, 2) }}%</td>
                                <td class="px-2 py-3">
                                    @if ($prediction->predicted_to_reoffend)
                                        <span
                                            class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-950 dark:text-red-300">Berpotensi
                                            Melanggar Lagi</span>
                                    @else
                                        <span
                                            class="inline-flex rounded-md bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Relatif
                                            Aman</span>
                                    @endif
                                </td>
                                <td class="px-2 py-3">{{ $prediction->suggestedViolation?->name ?? '-' }}</td>
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

            <div class="mt-4">
                {{ $predictions->links() }}
            </div>
        </section>
    </div>
</x-layouts::app>
