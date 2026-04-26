<x-layouts::app :title="__('Pelanggaran Santri')">
    <div class="space-y-4">
        @if (session('status'))
            <div
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Pelanggaran Santri</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Kelola pencatatan pelanggaran setiap santri dan
                        riwayatnya.</p>
                </div>
                <button type="button" onclick="document.getElementById('studentViolationCreateDialog').showModal()"
                    class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Tambah
                    Pelanggaran</button>
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('student-violations.index') }}"
                class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="w-full md:max-w-sm">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Filter Santri</label>
                    <select name="student_id"
                        class="mt-2 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                        <option value="">Semua Santri</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" @selected($selectedStudentId === $student->id)>{{ $student->nis }} -
                                {{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Terapkan</button>
            </form>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 text-left text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                            <th class="px-2 py-2">Tanggal</th>
                            <th class="px-2 py-2">Santri</th>
                            <th class="px-2 py-2">Pelanggaran</th>
                            <th class="px-2 py-2">Catatan</th>
                            <th class="px-2 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($studentViolations as $item)
                            <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                <td class="px-2 py-2">{{ $item->occurred_at?->format('d-m-Y') }}</td>
                                <td class="px-2 py-2">{{ $item->student?->nis }} - {{ $item->student?->name }}</td>
                                <td class="px-2 py-2">{{ $item->violation?->code }} - {{ $item->violation?->name }}
                                </td>
                                <td class="px-2 py-2">{{ $item->notes ?? '-' }}</td>
                                <td class="px-2 py-2">
                                    <div class="flex gap-2">
                                        <button type="button"
                                            onclick="document.getElementById('studentViolationEdit{{ $item->id }}').showModal()"
                                            class="rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-semibold text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Ubah</button>
                                        <form method="POST" action="{{ route('student-violations.destroy', $item) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg bg-red-500 px-3 py-1.5 text-xs font-semibold text-white"
                                                onclick="return confirm('Hapus data pelanggaran ini?')">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-2 py-6 text-center text-zinc-500 dark:text-zinc-400">Belum
                                    ada data pelanggaran santri.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $studentViolations->links() }}</div>
        </section>
    </div>

    <dialog id="studentViolationCreateDialog"
        class="w-full max-w-2xl rounded-2xl border border-zinc-200 p-0 backdrop:bg-black/30 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="p-4 space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Tambah Pelanggaran Santri</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Isi data pelanggaran yang terjadi pada santri.
                    </p>
                </div>
                <button type="button" class="text-zinc-400" onclick="this.closest('dialog').close()">&times;</button>
            </div>
            <form method="POST" action="{{ route('student-violations.store') }}" class="grid gap-3 md:grid-cols-2">
                @csrf
                <select name="student_id"
                    class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                    required>
                    <option value="">Pilih Santri</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->nis }} - {{ $student->name }}</option>
                    @endforeach
                </select>
                <select name="violation_id"
                    class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                    required>
                    <option value="">Pilih Pelanggaran</option>
                    @foreach ($violations as $violation)
                        <option value="{{ $violation->id }}">{{ $violation->code }} - {{ $violation->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="occurred_at"
                    class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                    required>
                <input type="text" name="notes" placeholder="Catatan"
                    class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                <div class="md:col-span-2 flex justify-end gap-2">
                    <button type="button" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm"
                        onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Simpan</button>
                </div>
            </form>
        </div>
    </dialog>

    @foreach ($studentViolations as $item)
        <dialog id="studentViolationEdit{{ $item->id }}"
            class="w-full max-w-2xl rounded-2xl border border-zinc-200 p-0 backdrop:bg-black/30 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Ubah Pelanggaran Santri</h3>
                    <button type="button" class="text-zinc-400"
                        onclick="this.closest('dialog').close()">&times;</button>
                </div>
                <form method="POST" action="{{ route('student-violations.update', $item) }}"
                    class="grid gap-3 md:grid-cols-2">
                    @csrf
                    @method('PUT')
                    <select name="student_id"
                        class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" @selected($student->id === $item->student_id)>{{ $student->nis }} -
                                {{ $student->name }}</option>
                        @endforeach
                    </select>
                    <select name="violation_id"
                        class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                        @foreach ($violations as $violation)
                            <option value="{{ $violation->id }}" @selected($violation->id === $item->violation_id)>{{ $violation->code }} -
                                {{ $violation->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="occurred_at" value="{{ $item->occurred_at?->format('Y-m-d') }}"
                        class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                    <input type="text" name="notes" value="{{ $item->notes }}"
                        class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="md:col-span-2 flex justify-end gap-2">
                        <button type="button" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm"
                            onclick="this.closest('dialog').close()">Batal</button>
                        <button type="submit"
                            class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Simpan</button>
                    </div>
                </form>
            </div>
        </dialog>
    @endforeach
</x-layouts::app>
