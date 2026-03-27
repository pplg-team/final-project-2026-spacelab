<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Ruang Kelas Saya') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto px-2 sm:px-4 lg:px-6">
            <!-- Header Card with Classroom Info -->
            <div class="bg-white  shadow-sm xl overflow-hidden
                            border border-gray-100  p-4 md:p-5
                            hover:shadow-md transition-all duration-150">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-6">
                        <!-- Classroom Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white  shadow-sm xl overflow-hidden
                                border border-gray-100 
                                hover:shadow-md transition-all duration-150 flex items-center justify-center">
                                    <x-heroicon-o-academic-cap class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 " />
                                </div>
                                <div>
                                    <h3 class="text-lg sm:text-2xl font-bold text-gray-900  leading-tight">
                                        {{ $classroom?->full_name ?? 'Belum ada kelas' }}
                                    </h3>
                                    @if($classroom)
                                        <p class="text-xs sm:text-sm text-gray-600  mt-1">
                                            {{ $classroom->major?->name ?? '' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Guardian Info -->
                        @if($guardian)
                            <div class="flex items-center gap-3 px-3 py-2 sm:px-6 sm:py-4 bg-white  shadow-sm xl overflow-hidden
                            border border-gray-100 
                            hover:shadow-md transition-all duration-150">
                                <div class="relative">
                                    <div class="w-10 h-10 sm:w-14 sm:h-14 overflow-hidden bg-gray-100  flex items-center justify-center text-gray-700  font-semibold ring-2 ring-gray-100 ">
                                        @if($guardian->teacher->avatar ?? false)
                                            <img src="{{ Storage::url($guardian->teacher->avatar) }}" alt="Wali Kelas" class="w-full h-full object-cover"/>
                                        @else
                                            <span class="text-lg">{{ $guardian->teacher->initials() }}</span>
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-gray-400 border-2 border-white  full"></div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500  uppercase tracking-wide">Wali Kelas</p>
                                    <p class="text-sm sm:text-base font-semibold text-gray-900  mt-0.5 max-w-xs sm:max-w-sm truncate">{{ $guardian->teacher->user->name }}</p>
                                </div>
                            </div>
                        @else
                            <div class="px-3 py-2 sm:px-6 sm:py-4 bg-gray-100  xl border border-gray-200 ">
                                <p class="text-sm text-gray-600 ">Belum ada wali kelas</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-6">
                <!-- Students List - 2/3 width -->
                <div class="lg:col-span-2">
                    <div class="bbg-white  shadow-sm xl overflow-hidden
                            border border-gray-100 
                            hover:shadow-md transition-all duration-150">
                        <div class="p-4 sm:p-6 border-b border-gray-200 ">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm sm:text-lg font-semibold text-gray-900  flex items-center gap-2">
                                    <x-heroicon-o-users class="w-4 h-4 sm:w-5 sm:h-5 text-gray-700 " />
                                    Daftar Teman Kelas
                                </h3>
                                <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-gray-100  text-gray-700  text-xs sm:text-sm font-medium full">
                                    {{ count($students) }} Siswa
                                </span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 ">
                                <thead class="bg-gray-50 ">
                                    <tr>
                                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">No</th>
                                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">Siswa</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white  divide-y divide-gray-100 ">
                                    @forelse($students as $index => $student)
                                        <tr class="transition-colors duration-150">
                                            <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                                <span class="text-sm font-medium text-gray-500 ">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-normal sm:whitespace-nowrap">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-8 h-8 sm:w-10 sm:h-10 overflow-hidden bg-gray-100  flex items-center justify-center text-gray-700  font-semibold text-sm shadow-sm">
                                                        @if($student->student?->avatar)
                                                            <img src="{{ Storage::url($student->student->avatar) }}" alt="avatar" class="w-full h-full object-cover" />
                                                        @else
                                                            {{ $student->initials() }}
                                                        @endif
                                                    </div>
                                                    <span class="text-sm sm:text-base font-medium text-gray-900  truncate">{{ $student->name }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-4 sm:px-6 py-6 text-center">
                                                <div class="flex flex-col items-center gap-2">
                                                    <x-heroicon-o-user-group class="w-8 h-8 sm:w-12 sm:h-12 text-gray-300 " />
                                                    <p class="text-sm text-gray-500 ">Belum ada siswa di kelas ini</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Schedule Sidebar - 1/3 width -->
                <div class="lg:col-span-1">
                    <div class="space-y-6">
                        <!-- Current Day Info -->
                        <div class="bg-white  xl shadow-sm border border-gray-200  overflow-hidden">
                            <div class="p-3 sm:p-5 bg-white  border-b border-gray-200 ">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-100  lg flex items-center justify-center shadow-sm">
                                        <x-heroicon-o-calendar class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 " />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-600  uppercase tracking-wide">Hari Ini</p>
                                        <div class="flex justify-between items-center gap-4">
                                            <p class="text-xs sm:text-sm font-semibold text-gray-900 ">{{ now()->translatedFormat('l, d F Y') }}</p>
                                            <p class="text-xs sm:text-sm font-semibold text-gray-900 ">{{ now()->translatedFormat('H:i') }}</p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Class -->
                        <div class="bg-white  xl shadow-sm border border-gray-200  overflow-hidden">
                            <div class="p-3 sm:p-5 border-b border-gray-200 ">
                                <h3 class="text-sm font-semibold text-gray-900  uppercase tracking-wide flex items-center gap-2">
                                    <div class="w-2 h-2 bg-gray-400 animate-pulse"></div>
                                    Pelajaran Saat Ini
                                </h3>
                            </div>

                            @if($currentEntry)
                                @php $curIsTeaching = $currentEntry->period?->is_teaching ?? true; @endphp
                                <div class="p-3 sm:p-5">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 text-sm text-gray-600 ">
                                            <x-heroicon-o-clock class="w-4 h-4" />
                                            <span>{{ optional($currentEntry->period)->start_time ? \Carbon\Carbon::parse(optional($currentEntry->period)->start_time)->format('H:i') : '-' }} - {{ optional($currentEntry->period)->end_time ? \Carbon\Carbon::parse(optional($currentEntry->period)->end_time)->format('H:i') : '-' }}</span>
                                        </div>

                                        <div>
                                            @if($curIsTeaching)
                                                <p class="text-sm sm:text-base font-bold text-gray-900 ">{{ $currentEntry->subject?->name ?? '-' }}</p>
                                            @else
                                                <div class="flex items-center gap-2">
                                                    <p class="px-2 py-1 bg-yellow-200  text-yellow-900 font-semibold text-xs">{{ $currentEntry->period?->ordinal ?? 'Pembiasaan' }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="pt-3 border-t border-gray-100  space-y-2">
                                            @if($curIsTeaching)
                                                <div class="flex items-center gap-2 text-sm text-gray-600 ">
                                                    <x-heroicon-o-user class="w-4 h-4" />
                                                    <span>{{ $currentEntry->teacher?->user?->name ?? '-' }}</span>
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-2 text-sm text-gray-600 ">
                                                <x-heroicon-c-globe-asia-australia class="w-5 h-5" />
                                                <span>{{ $currentEntry->roomHistory?->room?->name ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="p-4 sm:p-6 text-center">
                                    <x-heroicon-o-x-circle class="w-12 h-12 text-gray-300  mx-auto mb-3" />
                                    <p class="text-sm text-gray-500 ">Tidak ada pelajaran sedang berlangsung</p>
                                </div>
                            @endif
                        </div>

                        <!-- Today's Schedule -->
                        <div class="bg-white  xl shadow-sm border border-gray-200  overflow-hidden">
                            <div class="p-5 border-b border-gray-200 ">
                                <h3 class="text-sm font-semibold text-gray-900  uppercase tracking-wide">Jadwal Hari Ini</h3>
                            </div>

                            <div class="p-3 sm:p-5">
                                @if($todayEntries->isNotEmpty())
                                    <div class="space-y-2 max-h-60 sm:max-h-96 overflow-y-auto">
                                        @foreach($todayEntries as $entry)
                                            @php $entryIsTeaching = $entry->period?->is_teaching ?? true; @endphp
                                            <div class="p-3 sm:p-4 xl border transition-colors duration-150 {{ $entryIsTeaching ? 'bg-gray-50  border-gray-100  hover:border-gray-300 ' : 'bg-yellow-50  border-yellow-200 ' }}">
                                                <div class="flex justify-between items-start gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        @if($entryIsTeaching)
                                                            <p class="text-sm font-semibold text-gray-900  truncate">{{ $entry->subject?->name ?? '-' }}</p>
                                                            <p class="text-xs text-gray-600  mt-1 truncate">{{ $entry->teacher?->user?->name ?? '-' }}</p>
                                                        @else
                                                            <div class="flex items-center gap-2">
                                                                <p class="px-2 py-1 bg-yellow-200  text-yellow-900 font-semibold text-xs">{{ $entry->period?->ordinal ?? 'Pembiasaan' }}</p>
                                                            </div>
                                                            @if($entry->period?->description)
                                                                <p class="text-xs text-gray-500  mt-1 truncate">{{ $entry->period->description }}</p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <div class="text-xs font-medium text-gray-600  text-right">
                                                            <div>{{ optional($entry->period)->start_time ? \Carbon\Carbon::parse(optional($entry->period)->start_time)->format('H:i') : '-' }}</div>
                                                            <div>{{ optional($entry->period)->end_time ? \Carbon\Carbon::parse(optional($entry->period)->end_time)->format('H:i') : '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="py-6 sm:py-8 text-center">
                                        <x-heroicon-o-calendar-days class="w-12 h-12 text-gray-300  mx-auto mb-3" />
                                        <p class="text-sm text-gray-500 ">Tidak ada jadwal untuk hari ini</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
