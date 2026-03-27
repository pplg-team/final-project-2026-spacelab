<x-app-layout :title="$title" :description="$description">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800  leading-tight">
                {{ __('Kelola Jadwal Pelajaran') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('staff.schedules.templates.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white  border border-gray-300  md font-semibold text-xs text-gray-700  uppercase tracking-widest shadow-sm hover:bg-gray-50  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2  transition ease-in-out duration-150">
                    <x-heroicon-o-document-duplicate class="w-4 h-4 mr-2" />
                    Kelola Template
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-2 flex items-center justify-end ml-4">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600 ">
                {{ $activeTerm
                    ? 'Semester ' . ucfirst($activeTerm->kind) . ' Tahun Ajaran ' . $activeTerm->tahun_ajaran
                    : 'Tahun Ajaran Tidak Diketahui' }}
            </p>
        </div>
    </div>

    <div class="py-6">
        <div class="space-y-6">
            <!-- Alert Messages -->
            @if (session('success'))
                <div
                    class="bg-green-50  border border-green-200  lg p-4">
                    <div class="flex items-center">
                        <x-heroicon-o-check-circle
                            class="w-5 h-5 text-green-600  mr-3 flex-shrink-0" />
                        <p class="text-sm font-medium text-green-800 ">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50  border border-red-200  lg p-4">
                    <div class="flex items-center">
                        <x-heroicon-o-x-circle class="w-5 h-5 text-red-600  mr-3 flex-shrink-0" />
                        <p class="text-sm font-medium text-red-800 ">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50  border border-red-200  lg p-4">
                    <div class="flex items-start">
                        <x-heroicon-o-exclamation-triangle
                            class="w-5 h-5 text-red-600  mr-3 mt-0.5 flex-shrink-0" />
                        <div class="flex-1">
                            <p class="text-sm font-medium text-red-800  mb-2">Terdapat kesalahan:</p>
                            <ul class="list-disc list-inside text-sm text-red-700  space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="bg-white  shadow-sm sm:lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900  mb-4">Filter Jadwal</h3>
                    <form method="GET" action="{{ route('staff.schedules.index') }}"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Major Filter -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700  mb-1">Jurusan</label>
                            <select name="major_id" id="majorFilter"
                                class="w-full md border-gray-300    shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                onchange="this.form.submit()">
                                <option value="">Pilih Jurusan</option>
                                @foreach ($majors as $major)
                                    <option value="{{ $major->id }}"
                                        {{ $selectedMajorId == $major->id ? 'selected' : '' }}>
                                        {{ $major->code }} - {{ $major->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Class Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700  mb-1">Kelas</label>
                            <select name="class_id" id="classFilter"
                                class="w-full md border-gray-300    shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                onchange="this.form.submit()" {{ !$selectedMajorId ? 'disabled' : '' }}>
                                <option value="">Pilih Kelas</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                        {{ $class->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Template Filter -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700  mb-1">Template</label>
                            <select name="template_id" id="templateFilter"
                                class="w-full md border-gray-300    shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                onchange="this.form.submit()" {{ !$selectedClassId ? 'disabled' : '' }}>
                                <option value="">Pilih Template</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}"
                                        {{ $selectedTemplateId == $template->id ? 'selected' : '' }}>
                                        Versi {{ $template->version }} - {{ $template->block->name ?? 'Block' }}
                                        {{ $template->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="flex items-end">
                            <a href="{{ route('staff.schedules.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-100  border border-gray-300  md font-semibold text-xs text-gray-700  uppercase tracking-widest hover:bg-gray-200  transition">
                                <x-heroicon-o-arrow-path class="w-4 h-4 mr-1" />
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Schedule Grid -->
            @if ($selectedTemplate)
                <div class="bg-white  shadow-sm sm:lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 ">
                                    Jadwal Kelas {{ $selectedTemplate->class->full_name }}
                                </h3>
                                <p class="text-sm text-gray-500 ">
                                    Template Versi {{ $selectedTemplate->version }} -
                                    {{ $selectedTemplate->block->name ?? 'Block' }}
                                    @if ($selectedTemplate->is_active)
                                        <span
                                            class="ml-2 inline-flex items-center px-2 py-0.5  text-xs font-medium bg-green-100  text-green-800 ">
                                            Aktif
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Schedule Table -->
                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full divide-y divide-gray-200  border border-gray-200 ">
                                <thead class="bg-gray-50 ">
                                    <tr>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider border-r border-gray-200  w-20">
                                            Jam
                                        </th>
                                        @foreach ($days as $dayNum => $dayName)
                                            <th
                                                class="px-3 py-3 text-center text-xs font-medium text-gray-500  uppercase tracking-wider border-r border-gray-200  last:border-r-0">
                                                {{ $dayName }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white  divide-y divide-gray-200 ">
                                    @foreach ($periods as $period)
                                        <tr class="hover:bg-gray-50 ">
                                            <td
                                                class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900  border-r border-gray-200  bg-gray-50 ">
                                                <div class="text-center">
                                                    @if ($period->is_teaching)
                                                        <div class="font-bold">Jam {{ $period->ordinal }}</div>
                                                    @else
                                                        <div
                                                            class="text-xs uppercase font-extrabold text-orange-600 ">
                                                            {{ $period->ordinal }}
                                                        </div>
                                                    @endif
                                                    <div class="text-xs text-gray-500  mt-1">
                                                        {{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }}
                                                        - {{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}
                                                    </div>
                                                </div>
                                            </td>
                                            @foreach ($days as $dayNum => $dayName)
                                                @php
                                                    $cellKey = $dayNum . '-' . $period->id;
                                                    $cellEntry = $entries->get($cellKey)?->first();
                                                @endphp
                                                <td
                                                    class="px-2 py-2 text-sm border-r border-gray-200  last:border-r-0 align-top min-w-[150px]">
                                                    @if ($cellEntry)
                                                        <div
                                                            class="bg-indigo-50  lg p-2 relative group">
                                                            <div
                                                                class="font-medium text-indigo-900  text-xs">
                                                                {{ $cellEntry->teacherSubject?->subject?->name ?? '-' }}
                                                            </div>
                                                            <div
                                                                class="text-xs text-indigo-700  mt-1">
                                                                {{ $cellEntry->teacherSubject?->teacher?->user?->name ?? '-' }}
                                                            </div>
                                                            @if ($cellEntry->roomHistory?->room)
                                                                <div
                                                                    class="text-xs text-indigo-600  mt-1">
                                                                    <x-heroicon-o-map-pin class="w-3 h-3 inline" />
                                                                    {{ $cellEntry->roomHistory->room->name }}
                                                                </div>
                                                            @endif
                                                            <!-- Action buttons -->
                                                            <div
                                                                class="absolute top-1 right-1 hidden group-hover:flex gap-1">
                                                                <button type="button"
                                                                    onclick="editEntry('{{ $cellEntry->id }}', '{{ $cellEntry->teacher_subject_id }}', '{{ $cellEntry->room_history_id }}')"
                                                                    class="p-1 text-yellow-600 hover:bg-yellow-100   transition-colors">
                                                                    <x-heroicon-o-pencil class="w-3 h-3" />
                                                                </button>
                                                                <button type="button"
                                                                    onclick="deleteEntry('{{ $cellEntry->id }}')"
                                                                    class="p-1 text-red-600 hover:bg-red-100   transition-colors">
                                                                    <x-heroicon-o-trash class="w-3 h-3" />
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @if ($period->is_teaching)
                                                            <button type="button"
                                                                onclick="addEntry('{{ $selectedTemplateId }}', {{ $dayNum }}, '{{ $period->id }}')"
                                                                class="w-full h-16 flex items-center justify-center border-2 border-dashed border-gray-300  lg hover:border-indigo-400  hover:bg-indigo-50  transition-colors group">
                                                                <x-heroicon-o-plus
                                                                    class="w-5 h-5 text-gray-400 group-hover:text-indigo-500" />
                                                            </button>
                                                        @else
                                                            <div
                                                                class="w-full h-16 flex items-center justify-center bg-gray-100  lg border border-transparent">
                                                                <span
                                                                    class="text-[10px] uppercase font-bold text-gray-400  tracking-tighter">
                                                                    {{ $period->ordinal }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white  shadow-sm sm:lg">
                    <div class="p-12 text-center">
                        <x-heroicon-o-calendar-days class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                        <h3 class="text-sm font-medium text-gray-900 ">Pilih Filter</h3>
                        <p class="mt-1 text-sm text-gray-500 ">
                            Pilih jurusan, kelas, dan template untuk melihat jadwal
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Entry Modal -->
    <x-modal name="add-entry-modal" focusable>
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900 ">
                    Tambah Jadwal
                </h2>
                <button type="button" x-on:click="$dispatch('close')"
                    class="text-gray-400 hover:text-gray-600 ">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            <form id="addEntryForm" method="POST" action="{{ route('staff.schedules.entries.store') }}"
                class="space-y-4">
                @csrf
                <input type="hidden" name="template_id" id="addTemplateId">
                <input type="hidden" name="day_of_week" id="addDayOfWeek">
                <input type="hidden" name="period_id" id="addPeriodId">

                <div id="addEntryInfo"
                    class="bg-gray-50  lg p-3 text-sm text-gray-600 ">
                    <!-- Info will be populated by JS -->
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700  mb-1">Guru & Mata
                        Pelajaran *</label>
                    <select name="teacher_subject_id" id="addTeacherSubject" required
                        class="w-full md border-gray-300    shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Pilih Guru & Mata Pelajaran</option>
                        @foreach ($teacherSubjects as $ts)
                            <option value="{{ $ts->id }}">
                                {{ $ts->teacher?->user?->name ?? 'Guru' }} - {{ $ts->subject?->name ?? 'Mapel' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700  mb-1">Ruangan
                        (Opsional)</label>
                    <select name="room_history_id" id="addRoomHistory"
                        class="w-full md border-gray-300    shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Tanpa Ruangan</option>
                        @foreach ($roomHistories as $rh)
                            <option value="{{ $rh->id }}">
                                {{ $rh->room?->name ?? 'Ruangan' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div
                    class="bg-gray-50  px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 -mx-6 -mb-6 mt-6">
                    <button type="submit"
                        class="inline-flex w-full justify-center md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                        Simpan
                    </button>
                    <button type="button" x-on:click="$dispatch('close')"
                        class="mt-3 inline-flex w-full justify-center md bg-white  px-4 py-2 text-sm font-semibold text-gray-900  shadow-sm ring-1 ring-inset ring-gray-300  hover:bg-gray-50  sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Edit Entry Modal -->
    <x-modal name="edit-entry-modal" focusable>
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900 ">
                    Edit Jadwal
                </h2>
                <button type="button" x-on:click="$dispatch('close')"
                    class="text-gray-400 hover:text-gray-600 ">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            <form id="editEntryForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700  mb-1">Guru & Mata
                        Pelajaran *</label>
                    <select name="teacher_subject_id" id="editTeacherSubject" required
                        class="w-full md border-gray-300    shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Pilih Guru & Mata Pelajaran</option>
                        @foreach ($teacherSubjects as $ts)
                            <option value="{{ $ts->id }}">
                                {{ $ts->teacher?->user?->name ?? 'Guru' }} - {{ $ts->subject?->name ?? 'Mapel' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700  mb-1">Ruangan
                        (Opsional)</label>
                    <select name="room_history_id" id="editRoomHistory"
                        class="w-full md border-gray-300    shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Tanpa Ruangan</option>
                        @foreach ($roomHistories as $rh)
                            <option value="{{ $rh->id }}">
                                {{ $rh->room?->name ?? 'Ruangan' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div
                    class="bg-gray-50  px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 -mx-6 -mb-6 mt-6">
                    <button type="submit"
                        class="inline-flex w-full justify-center md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                        Simpan Perubahan
                    </button>
                    <button type="button" x-on:click="$dispatch('close')"
                        class="mt-3 inline-flex w-full justify-center md bg-white  px-4 py-2 text-sm font-semibold text-gray-900  shadow-sm ring-1 ring-inset ring-gray-300  hover:bg-gray-50  sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Delete Entry Modal -->
    <x-modal name="delete-entry-modal" focusable>
        <div class="p-6">
            <div class="flex items-start mb-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600 " />
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-lg font-medium text-gray-900 ">
                        Hapus Jadwal
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 ">
                        Apakah Anda yakin ingin menghapus jadwal ini? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>

            <form id="deleteEntryForm" method="POST">
                @csrf
                @method('DELETE')

                <div
                    class="bg-gray-50  px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 -mx-6 -mb-6 mt-6">
                    <button type="submit"
                        class="inline-flex w-full justify-center md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                        Hapus
                    </button>
                    <button type="button" x-on:click="$dispatch('close')"
                        class="mt-3 inline-flex w-full justify-center md bg-white  px-4 py-2 text-sm font-semibold text-gray-900  shadow-sm ring-1 ring-inset ring-gray-300  hover:bg-gray-50  sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        const days = @json($days);

        function addEntry(templateId, dayOfWeek, periodId) {
            document.getElementById('addTemplateId').value = templateId;
            document.getElementById('addDayOfWeek').value = dayOfWeek;
            document.getElementById('addPeriodId').value = periodId;

            const dayName = days[dayOfWeek] || 'Hari';
            document.getElementById('addEntryInfo').innerHTML = `<strong>Hari:</strong> ${dayName}`;

            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'add-entry-modal'
            }));
        }

        function editEntry(entryId, teacherSubjectId, roomHistoryId) {
            const form = document.getElementById('editEntryForm');
            form.action = `/staff/schedules/entries/${entryId}`;

            document.getElementById('editTeacherSubject').value = teacherSubjectId || '';
            document.getElementById('editRoomHistory').value = roomHistoryId || '';

            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'edit-entry-modal'
            }));
        }

        function deleteEntry(entryId) {
            const form = document.getElementById('deleteEntryForm');
            form.action = `/staff/schedules/entries/${entryId}`;

            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'delete-entry-modal'
            }));
        }
    </script>
</x-app-layout>
