<x-layouts::app :title="__('Data Santri')">
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
                    <h1 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Data Santri</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Kelola data santri, ubah data, hapus data, dan
                        impor data dari template.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('studentCreateDialog').showModal()"
                        class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Tambah
                        Santri</button>
                    <button type="button" onclick="document.getElementById('studentImportDialog').showModal()"
                        class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Import
                        Data</button>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Cari Santri</label>
            <form method="GET" action="{{ route('students.index') }}">
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Nama / NIS / Kamar"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800" />
            </form>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 text-left text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                            <th class="px-2 py-2">NIS</th>
                            <th class="px-2 py-2">Nama</th>
                            <th class="px-2 py-2">Gender</th>
                            <th class="px-2 py-2">Kamar</th>
                            <th class="px-2 py-2">Lembaga</th>
                            <th class="px-2 py-2">Status</th>
                            <th class="px-2 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                <td class="px-2 py-2">{{ $student->nis }}</td>
                                <td class="px-2 py-2">{{ $student->name }}</td>
                                <td class="px-2 py-2">{{ $student->gender == 'L' ? 'Laki-laki' : ($student->gender == 'P' ? 'Perempuan' : '-') }}</td>
                                <td class="px-2 py-2">{{ $student->room ? ($rooms[$student->room] ?? $student->room) : '-' }}</td>
                                <td class="px-2 py-2">{{ $student->lembaga ? ($lembagas[$student->lembaga] ?? $student->lembaga) : '-' }}</td>
                                <td class="px-2 py-2">
                                    <form method="POST" action="{{ route('students.update-status', $student) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="nonaktif">
                                        
                                        <label class="relative inline-flex cursor-pointer items-center" title="Ubah status">
                                            <input type="checkbox" name="status" value="aktif" class="peer sr-only"
                                                {{ $student->status === 'aktif' ? 'checked' : '' }}
                                                onchange="this.form.submit()">
                                            <div class="peer h-5 w-9 rounded-full bg-zinc-200 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:border after:border-zinc-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-emerald-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none dark:border-zinc-600 dark:bg-zinc-700 dark:peer-focus:ring-emerald-800"></div>
                                        </label>
                                    </form>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="flex gap-2">
                                        <button type="button"
                                            onclick="document.getElementById('studentEdit{{ $student->id }}').showModal()"
                                            class="rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-semibold text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Ubah</button>
                                        <form method="POST" action="{{ route('students.destroy', $student) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg bg-red-500 px-3 py-1.5 text-xs font-semibold text-white"
                                                onclick="return confirm('Hapus data santri ini?')">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-2 py-6 text-center text-zinc-500 dark:text-zinc-400">Belum
                                    ada data santri.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $students->links() }}</div>
        </section>
    </div>

    <dialog id="studentCreateDialog"
        class="w-full max-w-xl rounded-2xl border border-zinc-200 p-0 backdrop:bg-black/30 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="p-4">
            <div class="mb-3 flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Tambah Santri</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Isi data santri baru di dalam modal ini.</p>
                </div>
                <button type="button" class="text-zinc-400" onclick="this.closest('dialog').close()">&times;</button>
            </div>
            <form method="POST" action="{{ route('students.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium">NIS</label>
                    <input type="text" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="nis"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Nama Santri</label>
                    <input type="text" name="name"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Jenis Kelamin</label>
                    <select name="gender" class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div x-data="{ type: 'room' }">
                    <label class="mb-1 block text-sm font-medium">Asrama</label>
                    <select name="type" x-model="type" class="mb-3 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                        <option value="room">Kamar</option>
                        <option value="lembaga">Lembaga</option>
                    </select>

                    <div x-show="type === 'room'">
                        <label class="mb-1 block text-sm font-medium">Kamar</label>
                        <select name="room" x-bind:required="type === 'room'" class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Kamar</option>
                            @foreach($rooms as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="type === 'lembaga'">
                        <label class="mb-1 block text-sm font-medium">Lembaga</label>
                        <select name="lembaga" x-bind:required="type === 'lembaga'" class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Lembaga</option>
                            @foreach($lembagas as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Status</label>
                    <select name="status"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                        <option value="aktif">aktif</option>
                        <option value="nonaktif">nonaktif</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm"
                        onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Simpan</button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="studentImportDialog"
        class="w-full max-w-xl rounded-2xl border border-zinc-200 p-0 backdrop:bg-black/30 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="p-4">
            <div class="mb-3 flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Import Data Santri (Excel)</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Download template terlebih dahulu, lalu unggah
                        file yang sudah diisi.</p>
                </div>
                <button type="button" class="text-zinc-400" onclick="this.closest('dialog').close()">&times;</button>
            </div>
            <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data"
                class="space-y-3">
                @csrf
                <a href="{{ route('students.export') }}"
                    class="inline-flex rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700">Download
                    template</a>
                <div>
                    <label class="mb-1 block text-sm font-medium">File xlsx/xls/csv</label>
                    <input type="file" name="file"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm"
                        onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900">Import</button>
                </div>
            </form>
        </div>
    </dialog>

    @foreach ($students as $student)
        <dialog id="studentEdit{{ $student->id }}"
            class="w-full max-w-xl rounded-2xl border border-zinc-200 p-0 backdrop:bg-black/30 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="p-4">
                <div class="mb-3 flex items-start justify-between">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Ubah Santri</h3>
                    <button type="button" class="text-zinc-400"
                        onclick="this.closest('dialog').close()">&times;</button>
                </div>
                <form method="POST" action="{{ route('students.update', $student) }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <input type="text" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="nis" value="{{ $student->nis }}"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                    <input type="text" name="name" value="{{ $student->name }}"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        required>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Jenis Kelamin</label>
                        <select name="gender" class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" @selected($student->gender === 'L')>Laki-laki</option>
                            <option value="P" @selected($student->gender === 'P')>Perempuan</option>
                        </select>
                    </div>
                    <div x-data="{ type: '{{ $student->lembaga ? 'lembaga' : 'room' }}' }">
                        <label class="mb-1 block text-sm font-medium">Asrama</label>
                        <select name="type" x-model="type" class="mb-3 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="room">Kamar</option>
                            <option value="lembaga">Lembaga</option>
                        </select>

                        <div x-show="type === 'room'">
                            <label class="mb-1 block text-sm font-medium">Kamar</label>
                            <select name="room" x-bind:required="type === 'room'" class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="">Pilih Kamar</option>
                                @foreach($rooms as $code => $name)
                                    <option value="{{ $code }}" @selected($student->room === $code)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="type === 'lembaga'">
                            <label class="mb-1 block text-sm font-medium">Lembaga</label>
                            <select name="lembaga" x-bind:required="type === 'lembaga'" class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="">Pilih Lembaga</option>
                                @foreach($lembagas as $code => $name)
                                    <option value="{{ $code }}" @selected($student->lembaga === $code)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <select name="status"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                        <option value="aktif" @selected($student->status === 'aktif')>aktif</option>
                        <option value="nonaktif" @selected($student->status === 'nonaktif')>nonaktif</option>
                    </select>
                    <div class="flex justify-end gap-2 pt-2">
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
