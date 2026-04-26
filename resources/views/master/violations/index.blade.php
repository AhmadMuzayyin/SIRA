<x-layouts::app :title="__('Master Pelanggaran')">
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
                    <h1 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Master Pelanggaran</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Kelola pelanggaran, ubah data, hapus data, dan
                        impor data dari template.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('violationCreateDialog').showModal()"
                        class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Tambah
                        Pelanggaran</button>
                    <button type="button" onclick="document.getElementById('violationImportDialog').showModal()"
                        class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Import
                        Data</button>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 text-left text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                            <th class="px-2 py-2">Kode</th>
                            <th class="px-2 py-2">Nama Pelanggaran</th>
                            <th class="px-2 py-2">Kriteria</th>
                            <th class="px-2 py-2">Kategori</th>
                            <th class="px-2 py-2">Poin</th>
                            <th class="px-2 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($violations as $violation)
                            <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                <td class="px-2 py-2">{{ $violation->code }}</td>
                                <td class="px-2 py-2">{{ $violation->name }}</td>
                                <td class="px-2 py-2">{{ $violation->criterion?->name }}</td>
                                <td class="px-2 py-2">{{ $violation->criterion?->category }}</td>
                                <td class="px-2 py-2">{{ $violation->points }}</td>
                                <td class="px-2 py-2">
                                    <div class="flex gap-2">
                                        <button type="button"
                                            onclick="document.getElementById('violationEdit{{ $violation->id }}').showModal()"
                                            class="rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-semibold text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Ubah</button>
                                        <form method="POST" action="{{ route('violations.destroy', $violation) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg bg-red-500 px-3 py-1.5 text-xs font-semibold text-white"
                                                onclick="return confirm('Hapus data ini?')">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-2 py-6 text-center text-zinc-500 dark:text-zinc-400">Belum
                                    ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $violations->links() }}</div>
        </section>
    </div>

    <dialog id="violationCreateDialog"
        class="w-full max-w-xl rounded-2xl border border-zinc-200 p-0 backdrop:bg-black/30 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="p-4 space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Tambah Pelanggaran</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Isi data pelanggaran baru di dalam modal ini.
                    </p>
                </div>
                <button type="button" class="text-zinc-400" onclick="this.closest('dialog').close()">&times;</button>
            </div>
            <form method="POST" action="{{ route('violations.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="mb-1 block text-sm">Kriteria</label>
                    <select name="violation_criterion_id"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                        @foreach ($criteria as $criterion)
                            <option value="{{ $criterion->id }}">{{ $criterion->name }} ({{ $criterion->category }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="text" name="code" placeholder="Kode"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                    required>
                <input type="text" name="name" placeholder="Nama Pelanggaran"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                    required>
                <input type="number" name="points" value="1" min="1"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                    required>
                <div class="flex justify-end gap-2">
                    <button type="button" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm"
                        onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Simpan</button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="violationImportDialog"
        class="w-full max-w-xl rounded-2xl border border-zinc-200 p-0 backdrop:bg-black/30 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="p-4 space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Import Pelanggaran (Excel)</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Download template terlebih dahulu, lalu unggah
                        file yang sudah diisi.</p>
                </div>
                <button type="button" class="text-zinc-400" onclick="this.closest('dialog').close()">&times;</button>
            </div>
            <form method="POST" action="{{ route('violations.import') }}" enctype="multipart/form-data"
                class="space-y-3">
                @csrf
                <a href="{{ route('violations.export') }}"
                    class="inline-flex rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700">Download
                    template</a>
                <input type="file" name="file"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                    required>
                <div class="flex justify-end gap-2">
                    <button type="button" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm"
                        onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Import</button>
                </div>
            </form>
        </div>
    </dialog>

    @foreach ($violations as $violation)
        <dialog id="violationEdit{{ $violation->id }}"
            class="w-full max-w-xl rounded-2xl border border-zinc-200 p-0 backdrop:bg-black/30 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="p-4">
                <div class="mb-3 flex items-start justify-between">
                    <h3 class="text-lg font-semibold">Ubah Pelanggaran</h3>
                    <button type="button" class="text-zinc-400"
                        onclick="this.closest('dialog').close()">&times;</button>
                </div>
                <form method="POST" action="{{ route('violations.update', $violation) }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <select name="violation_criterion_id"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                        @foreach ($criteria as $criterion)
                            <option value="{{ $criterion->id }}" @selected($criterion->id === $violation->violation_criterion_id)>{{ $criterion->name }}
                                ({{ $criterion->category }})</option>
                        @endforeach
                    </select>
                    <input type="text" name="code" value="{{ $violation->code }}"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                    <input type="text" name="name" value="{{ $violation->name }}"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                    <input type="number" name="points" value="{{ $violation->points }}" min="1"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                    <div class="flex justify-end gap-2">
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
