<x-app-layout :title="$title" :description="$description">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('staff.reports.index') }}"
                class="p-2 hover:bg-gray-100  lg transition-colors">
                <x-heroicon-o-arrow-left class="w-5 h-5 text-gray-600 " />
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800  leading-tight">
                    {{ __('Laporan Guru') }}
                </h2>
                <p class="text-sm text-gray-500 ">Data guru dan beban mengajar</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    class="bg-white  xl shadow-sm border border-gray-100  p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-emerald-100  xl">
                            <x-heroicon-o-user-group class="w-6 h-6 text-emerald-600 " />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 ">Total Guru</p>
                            <p class="text-2xl font-bold text-gray-900 ">{{ $teachers->count() }}</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white  xl shadow-sm border border-gray-100  p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100  xl">
                            <x-heroicon-o-clock class="w-6 h-6 text-blue-600 " />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 ">Total Jam Mengajar</p>
                            <p class="text-2xl font-bold text-gray-900 ">{{ array_sum($teachingLoads) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white  xl shadow-sm border border-gray-100  p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-violet-100  xl">
                            <x-heroicon-o-calculator class="w-6 h-6 text-violet-600 " />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 ">Rata-rata Beban</p>
                            <p class="text-2xl font-bold text-gray-900 ">
                                {{ $teachers->count() > 0 ? round(array_sum($teachingLoads) / $teachers->count(), 1) : 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Button -->
            <div class="flex justify-end">
                <a href="{{ route('staff.reports.teachers.export') }}"
                    class="inline-flex items-center px-4 py-2.5 bg-emerald-600 border border-transparent lg font-medium text-sm text-white hover:bg-emerald-700 transition">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                    Export CSV
                </a>
            </div>

            <!-- Teachers Table -->
            <div
                class="bg-white  shadow-sm xl border border-gray-100  overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 ">
                        <thead class="bg-gray-50 ">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider w-16">
                                    No
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Guru
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Kode
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Kontak
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Mata Pelajaran
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Beban Mengajar
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white  divide-y divide-gray-200 ">
                            @forelse ($teachers as $index => $teacher)
                                <tr class="hover:bg-gray-50  transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if ($teacher->avatar)
                                                    <img class="h-10 w-10 object-cover"
                                                        src="{{ Storage::url($teacher->avatar) }}" alt="">
                                                @else
                                                    <div
                                                        class="h-10 w-10 bg-emerald-100  flex items-center justify-center">
                                                        <span
                                                            class="text-sm font-medium text-emerald-600 ">
                                                            {{ strtoupper(substr($teacher->user->name ?? 'T', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 ">
                                                    {{ $teacher->user->name ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 md text-xs font-mono font-medium bg-gray-100  text-gray-800 ">
                                            {{ $teacher->code ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 ">
                                            {{ $teacher->user->email ?? '-' }}</div>
                                        <div class="text-sm text-gray-500 ">
                                            {{ $teacher->phone ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($teacher->teacherSubjects->take(3) as $ts)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5  text-xs font-medium bg-blue-100  text-blue-700 ">
                                                    {{ $ts->subject->name ?? '-' }}
                                                </span>
                                            @empty
                                                <span class="text-sm text-gray-400 ">-</span>
                                            @endforelse
                                            @if ($teacher->teacherSubjects->count() > 3)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5  text-xs font-medium bg-gray-100  text-gray-600 ">
                                                    +{{ $teacher->teacherSubjects->count() - 3 }} lainnya
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $load = $teachingLoads[$teacher->id] ?? 0;
                                            $loadColor =
                                                $load > 20
                                                    ? 'text-red-600 bg-red-100  '
                                                    : ($load > 10
                                                        ? 'text-amber-600 bg-amber-100  '
                                                        : 'text-emerald-600 bg-emerald-100  ');
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-sm font-semibold {{ $loadColor }}">
                                            {{ $load }} jam
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <x-heroicon-o-user-group class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                                        <h3 class="text-sm font-medium text-gray-900 ">Tidak ada data
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500 ">Belum ada guru
                                            terdaftar</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($teachers->count() > 0)
                    <div class="bg-gray-50  px-6 py-4 border-t border-gray-200 ">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600 ">
                                Menampilkan {{ $teachers->count() }} guru
                            </p>
                            <a href="{{ route('staff.reports.teachers.export') }}"
                                class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent lg font-medium text-sm text-white hover:bg-emerald-700 transition">
                                <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                                Download CSV
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
