<x-app-layout :title="$title" :description="$description">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('staff.reports.index') }}"
                class="p-2 hover:bg-gray-100  lg transition-colors">
                <x-heroicon-o-arrow-left class="w-5 h-5 text-gray-600 " />
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800  leading-tight">
                    {{ __('Laporan Jadwal') }}
                </h2>
                <p class="text-sm text-gray-500 ">Jadwal pelajaran per kelas</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="space-y-6">
            <!-- Filter Section -->
            <div class="bg-white  shadow-sm xl border border-gray-100 ">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900  mb-4">Filter Jadwal</h3>
                    <form method="GET" action="{{ route('staff.reports.schedules') }}"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Major Filter -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700  mb-1">Jurusan</label>
                            <select name="major_id" id="majorFilter"
                                class="w-full lg border-gray-300    shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm"
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
                                class="w-full lg border-gray-300    shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm"
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

                        <!-- Actions -->
                        <div class="flex items-end gap-2">
                            <a href="{{ route('staff.reports.schedules') }}"
                                class="inline-flex items-center px-4 py-2.5 bg-gray-100  border border-gray-300  lg font-medium text-sm text-gray-700  hover:bg-gray-200  transition">
                                <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                                Reset
                            </a>
                            @if ($selectedClassId && $scheduleData)
                                <button type="button" onclick="window.print()"
                                    class="inline-flex items-center px-4 py-2.5 bg-violet-600 border border-transparent lg font-medium text-sm text-white hover:bg-violet-700 transition">
                                    <x-heroicon-o-printer class="w-4 h-4 mr-2" />
                                    Cetak
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Schedule Grid -->
            @if ($selectedClassId && $scheduleData !== null)
                <div
                    class="bg-white  shadow-sm xl border border-gray-100  overflow-hidden print:shadow-none print:border-0">
                    <div class="p-6 border-b border-gray-100  print:p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 ">
                                    Jadwal Kelas {{ $selectedClass->full_name }}
                                </h3>
                                <p class="text-sm text-gray-500  mt-1">
                                    {{ $selectedClass->major->name ?? '-' }}
                                </p>
                            </div>
                            <span
                                class="inline-flex items-center px-3 py-1 text-xs font-medium bg-violet-100  text-violet-700  print:hidden">
                                Template Aktif
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200  border-collapse">
                            <thead class="bg-gray-50 ">
                                <tr>
                                    <th
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider border-r border-gray-200  w-24 print:text-[10px]">
                                        Jam
                                    </th>
                                    @foreach ($days as $dayNum => $dayName)
                                        <th
                                            class="px-3 py-3 text-center text-xs font-medium text-gray-500  uppercase tracking-wider border-r border-gray-200  last:border-r-0 print:text-[10px]">
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
                                                    <div class="font-bold text-xs">Jam {{ $period->ordinal }}</div>
                                                @else
                                                    <div
                                                        class="text-xs uppercase font-extrabold text-orange-600 ">
                                                        {{ $period->ordinal }}
                                                    </div>
                                                @endif
                                                <div class="text-[10px] text-gray-500  mt-0.5">
                                                    {{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}
                                                </div>
                                            </div>
                                        </td>
                                        @foreach ($days as $dayNum => $dayName)
                                            @php
                                                $cellKey = $dayNum . '-' . $period->id;
                                                $cellEntry = $scheduleData->get($cellKey)?->first();
                                            @endphp
                                            <td
                                                class="px-2 py-2 text-sm border-r border-gray-200  last:border-r-0 align-top min-w-[140px] print:min-w-[100px]">
                                                @if ($cellEntry)
                                                    <div
                                                        class="bg-violet-50  lg p-2 print:p-1">
                                                        <div
                                                            class="font-medium text-violet-900  text-xs">
                                                            {{ $cellEntry->teacherSubject?->subject?->name ?? '-' }}
                                                        </div>
                                                        <div
                                                            class="text-[10px] text-violet-700  mt-1">
                                                            {{ $cellEntry->teacherSubject?->teacher?->user?->name ?? '-' }}
                                                        </div>
                                                        @if ($cellEntry->roomHistory?->room)
                                                            <div
                                                                class="text-[10px] text-violet-600  mt-0.5 flex items-center gap-0.5">
                                                                <x-heroicon-o-map-pin class="w-3 h-3 inline" />
                                                                {{ $cellEntry->roomHistory->room->name }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif (!$period->is_teaching)
                                                    <div
                                                        class="w-full h-12 flex items-center justify-center bg-gray-100  lg">
                                                        <span
                                                            class="text-[10px] uppercase font-bold text-gray-400 ">
                                                            {{ $period->ordinal }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-full h-12 flex items-center justify-center text-gray-300 ">
                                                        -
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="bg-gray-50  px-6 py-4 border-t border-gray-200  print:hidden">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600 ">
                                Jadwal Kelas {{ $selectedClass->full_name }}
                            </p>
                            <button type="button" onclick="window.print()"
                                class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent lg font-medium text-sm text-white hover:bg-violet-700 transition">
                                <x-heroicon-o-printer class="w-4 h-4 mr-2" />
                                Cetak Jadwal
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($selectedClassId && $scheduleData === null)
                <!-- No Schedule Found -->
                <div class="bg-white  shadow-sm xl border border-gray-100 ">
                    <div class="p-12 text-center">
                        <x-heroicon-o-calendar-days class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                        <h3 class="text-sm font-medium text-gray-900 ">Tidak ada jadwal</h3>
                        <p class="mt-1 text-sm text-gray-500 ">
                            Belum ada template jadwal aktif untuk kelas {{ $selectedClass->full_name }}
                        </p>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white  shadow-sm xl border border-gray-100 ">
                    <div class="p-12 text-center">
                        <x-heroicon-o-funnel class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                        <h3 class="text-sm font-medium text-gray-900 ">Pilih Filter</h3>
                        <p class="mt-1 text-sm text-gray-500 ">
                            Pilih jurusan dan kelas untuk menampilkan jadwal
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .print\:shadow-none,
            .print\:shadow-none * {
                visibility: visible;
            }

            .print\:shadow-none {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</x-app-layout>
