<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-900 ">
            Jadwal Pelajaran
        </h2>
    </x-slot>

    @php
        $currentTime = $currentTime ?? \Carbon\Carbon::now();
        $currentDayIndex = $currentDayIndex ?? (int) $currentTime->format('N');
        $dayNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu', 'Minggu'];
    @endphp

    <div class="py-8 min-h-screen bg-slate-50 ">
        <main class="mx-auto max-w-7xl px-4 md:px-10 space-y-8">

            <section class="grid grid-cols-1 xl:grid-cols-12 gap-6">

                <!-- Main Content -->
                <div class="xl:col-span-9 space-y-8">
                    @if (empty($allSchedules) || count($allSchedules) === 0)
                        <div class="border border-slate-200 bg-white p-6 text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 mb-4 shadow-sm">
                                <span class="text-3xl">🗓️</span>
                            </div>
                            <h4 class="text-lg font-bold text-slate-900 mb-1">
                                Tidak Ada Jadwal
                            </h4>
                            <p class="text-sm text-slate-500">
                                Belum ada jadwal pelajaran yang tersedia untuk ditampilkan.
                            </p>
                        </div>
                    @else
                        @foreach ($allSchedules as $day => $schedules)
                            @php
                                $schedules = $schedules ?? collect();
                                $processedSchedules = [];

                                if ($schedules->isNotEmpty()) {
                                    $values = $schedules->values();
                                    foreach ($values as $item) {
                                        if (empty($item)) {
                                            continue;
                                        }

                                        $isPeriodOnly = isset($item->is_period_only) && $item->is_period_only;

                                        if ($isPeriodOnly) {
                                            $processedSchedules[] = (object) [
                                                'type' => 'period',
                                                'period' => $item->period ?? null,
                                                'start_time' =>
                                                    $item->period->start_time ??
                                                    ($item->period->start_date?->format('H:i:s') ?? null),
                                                'end_time' =>
                                                    $item->period->end_time ??
                                                    ($item->period->end_date?->format('H:i:s') ?? null),
                                                'subject' => null,
                                                'teacher' => null,
                                                'template' => null,
                                                'roomHistory' => null,
                                                'day_of_week' => $item->day_of_week ?? $day,
                                                'item' => $item,
                                                'is_period_only' => true,
                                            ];
                                        } else {
                                            $processedSchedules[] = (object) [
                                                'type' => 'class',
                                                'period' => $item->period ?? null,
                                                'start_time' =>
                                                    $item->period->start_time ??
                                                    ($item->period->start_date?->format('H:i:s') ?? null),
                                                'end_time' =>
                                                    $item->period->end_time ??
                                                    ($item->period->end_date?->format('H:i:s') ?? null),
                                                'subject' => $item->teacherSubject->subject ?? null,
                                                'teacher' => $item->teacherSubject->teacher ?? null,
                                                'template' => $item->template ?? null,
                                                'roomHistory' => $item->roomHistory ?? null,
                                                'day_of_week' => $item->day_of_week ?? $day,
                                                'item' => $item,
                                                'is_period_only' => false,
                                            ];
                                        }
                                    }
                                }
                            @endphp

                            <div id="day-{{ $day }}" class="scroll-mt-24 space-y-4">
                                <div class="flex items-center gap-3">
                                    <h2 class="text-xl font-bold text-slate-900 ">
                                        {{ $dayNames[$day] ?? 'Hari' }}
                                    </h2>
                                    <div class="flex-1 h-px bg-slate-200"></div>
                                </div>

                                <div class="border border-slate-200 bg-white">
                                    <div class="p-4 sm:p-5">
                                        @if (empty($processedSchedules) || count($processedSchedules) === 0)
                                            <div class="text-center py-6">
                                                <p class="text-xs font-semibold text-slate-400">
                                                    Tidak ada jadwal untuk hari ini.
                                                </p>
                                            </div>
                                        @else
                                            <div class="space-y-3">
                                                @foreach ($processedSchedules as $schedule)
                                                    @php
                                                        $startTimeStr = $schedule->start_time ?? null;
                                                        $endTimeStr = $schedule->end_time ?? null;
                                                        $scheduleDay = $schedule->day_of_week ?? $day;

                                                        $startCarbon = $startTimeStr
                                                            ? \Carbon\Carbon::parse($startTimeStr)
                                                            : null;
                                                        $endCarbon = $endTimeStr
                                                            ? \Carbon\Carbon::parse($endTimeStr)
                                                            : null;

                                                        $isOngoing = false;
                                                        $isPast = false;

                                                        if ((int) $scheduleDay === (int) $currentDayIndex) {
                                                            if ($startCarbon && $endCarbon && $currentTime) {
                                                                $isOngoing = $currentTime->between(
                                                                    $startCarbon,
                                                                    $endCarbon,
                                                                );
                                                                $isPast = $endCarbon->lt($currentTime);
                                                            }
                                                        }

                                                        $isTeaching = $schedule->type === 'class';
                                                        $period = $schedule->period;

                                                        $formattedStart = $startTimeStr
                                                            ? \Carbon\Carbon::parse($startTimeStr)->format('H:i')
                                                            : '-';
                                                        $formattedEnd = $endTimeStr
                                                            ? \Carbon\Carbon::parse($endTimeStr)->format('H:i')
                                                            : '-';

                                                        $subjectName =
                                                            data_get($schedule, 'subject.name') ??
                                                            'Pembiasaan / Istirahat';
                                                        $subjectCode = data_get($schedule, 'subject.code');

                                                        $teacherName =
                                                            data_get($schedule, 'teacher.user.name') ??
                                                            (data_get($schedule, 'teacher.name') ?? '-');

                                                        $teacherAvatar =
                                                            data_get($schedule, 'teacher.user.avatar') ??
                                                            (data_get($schedule, 'teacher.avatar') ??
                                                                data_get($schedule, 'teacher.avatar_url'));

                                                        if (
                                                            $teacherAvatar &&
                                                            filter_var($teacherAvatar, FILTER_VALIDATE_URL)
                                                        ) {
                                                            $teacherAvatarUrl = $teacherAvatar;
                                                        } elseif (
                                                            $teacherAvatar &&
                                                            Storage::disk('public')->exists($teacherAvatar)
                                                        ) {
                                                            $teacherAvatarUrl = Storage::url($teacherAvatar);
                                                        } else {
                                                            $teacherAvatarUrl = asset('images/default-teacher.png');
                                                        }

                                                        $className = $schedule->template?->class?->full_name ?? '-';
                                                        $roomName = $schedule->roomHistory?->room?->name ?? '-';
                                                        $buildingName =
                                                            $schedule->roomHistory?->room?->building?->name ?? null;

                                                        $cardClasses = '';
                                                        if (!$isTeaching) {
                                                            $cardClasses = 'bg-amber-50 border-amber-200';
                                                        } elseif ($isOngoing) {
                                                            $cardClasses = 'bg-sky-50 border-sky-200 shadow-sm';
                                                        } elseif ($isPast) {
                                                            $cardClasses = 'bg-slate-50 border-slate-200 opacity-75';
                                                        } else {
                                                            $cardClasses = 'bg-white border-slate-200 hover:shadow-sm';
                                                        }
                                                    @endphp

                                                    <div class="relative group">
                                                        @if ($isOngoing)
                                                            <div
                                                                class="absolute -left-2 top-0 bottom-0 w-1 h-full bg-sky-400 shadow animate-pulse">
                                                            </div>
                                                        @endif

                                                        <div
                                                            class="relative overflow-hidden border {{ $cardClasses }}">
                                                            @if ($isOngoing)
                                                                <div
                                                                    class="absolute top-0 right-0 bg-sky-500 text-white px-2 py-1 shadow flex items-center gap-1.5 z-10">
                                                                    <span class="relative flex h-2 w-2">
                                                                        <span
                                                                            class="animate-ping absolute inline-flex h-full w-full bg-white opacity-75"></span>
                                                                        <span
                                                                            class="relative inline-flex h-2 w-2 bg-white"></span>
                                                                    </span>
                                                                    <span
                                                                        class="text-[9px] font-bold tracking-wide">BERLANGSUNG</span>
                                                                </div>
                                                            @endif

                                                            @if ($isPast)
                                                                <div
                                                                    class="absolute top-2 right-2 bg-slate-200 text-slate-600 px-2 py-0.5 shadow-sm z-10">
                                                                    <span
                                                                        class="text-[9px] font-semibold tracking-wider">SELESAI</span>
                                                                </div>
                                                            @endif

                                                            <div class="p-3 sm:p-4">
                                                                <div class="flex flex-row gap-3 sm:gap-4">
                                                                    <!-- Left Time Block (Compact) -->
                                                                    <div class="flex-shrink-0">
                                                                        <div
                                                                            class="inline-flex flex-col items-center justify-center p-2 min-w-[70px] sm:min-w-[80px] shadow-sm {{ $isOngoing ? 'bg-sky-500 text-white ring-1 ring-sky-300' : 'bg-slate-100 text-slate-800' }}">
                                                                            <div
                                                                                class="text-xl sm:text-2xl font-bold leading-none">
                                                                                {{ $formattedStart }}
                                                                            </div>
                                                                            <div
                                                                                class="text-[10px] opacity-80 mt-1 mb-0.5">
                                                                                s.d.</div>
                                                                            <div
                                                                                class="text-sm sm:text-base font-semibold leading-none">
                                                                                {{ $formattedEnd }}
                                                                            </div>
                                                                            @if ($period?->ordinal)
                                                                                <div
                                                                                    class="mt-2 px-2 py-0.5 bg-white/25 backdrop-blur-sm w-full text-center">
                                                                                    <span
                                                                                        class="text-[10px] sm:text-xs font-bold whitespace-nowrap">Jam
                                                                                        {{ $period->ordinal }}</span>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <!-- Right Details Block (Compact) -->
                                                                    <div
                                                                        class="flex-1 min-w-0 flex flex-col justify-center">
                                                                        <div class="mb-2.5">
                                                                            @if (!$isTeaching)
                                                                                <div class="flex items-center gap-2">
                                                                                    <h4
                                                                                        class="text-base sm:text-lg font-bold text-slate-900 leading-tight">
                                                                                        {{ $period?->name ?? 'Istirahat' }}
                                                                                    </h4>
                                                                                </div>
                                                                            @else
                                                                                <div
                                                                                    class="flex flex-wrap items-center gap-2">
                                                                                    <h4
                                                                                        class="text-base sm:text-lg font-bold text-slate-900 leading-tight">
                                                                                        {{ $subjectName }}
                                                                                    </h4>
                                                                                    @if ($subjectCode)
                                                                                        <span
                                                                                            class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-sky-100 text-sky-700">
                                                                                            {{ $subjectCode }}
                                                                                        </span>
                                                                                    @endif
                                                                                </div>
                                                                            @endif
                                                                        </div>

                                                                        <div
                                                                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                                                            @if ($isTeaching)
                                                                                <!-- Teacher Box -->
                                                                                <div
                                                                                    class="flex items-center gap-2.5 bg-white p-2 shadow-sm border border-slate-100">
                                                                                    <img src="{{ $teacherAvatarUrl }}"
                                                                                        alt="Guru"
                                                                                        class="w-8 h-8 object-cover border shadow-sm {{ $isOngoing ? 'border-sky-300' : 'border-slate-200' }}"
                                                                                        onerror="this.src='{{ asset('images/default-teacher.png') }}'">
                                                                                    <div class="flex-1 min-w-0">
                                                                                        <p
                                                                                            class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-0">
                                                                                            Pengajar</p>
                                                                                        <p
                                                                                            class="font-bold text-xs text-slate-800 truncate">
                                                                                            {{ $teacherName }}</p>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Class Box -->
                                                                                <div
                                                                                    class="flex items-center gap-2.5 bg-white p-2 shadow-sm border border-slate-100">
                                                                                    <div
                                                                                        class="flex-shrink-0 w-8 h-8 bg-slate-100 flex items-center justify-center border border-slate-200 shadow-sm">
                                                                                        <x-heroicon-s-users
                                                                                            class="h-4 w-4 text-slate-500" />
                                                                                    </div>
                                                                                    <div class="flex-1 min-w-0">
                                                                                        <p
                                                                                            class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-0">
                                                                                            Kelas</p>
                                                                                        <p
                                                                                            class="font-bold text-xs text-slate-800 truncate">
                                                                                            {{ $className }}</p>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Room Box -->
                                                                                <div
                                                                                    class="flex items-center gap-2.5 bg-white p-2 shadow-sm border border-slate-100">
                                                                                    <div
                                                                                        class="flex-shrink-0 w-8 h-8 bg-slate-100 flex items-center justify-center border border-slate-200 shadow-sm">
                                                                                        <x-heroicon-s-map-pin
                                                                                            class="w-4 h-4 text-slate-500" />
                                                                                    </div>
                                                                                    <div class="flex-1 min-w-0">
                                                                                        <p
                                                                                            class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-0">
                                                                                            Ruangan</p>
                                                                                        <p
                                                                                            class="font-bold text-xs text-slate-800 truncate">
                                                                                            {{ $roomName }}
                                                                                            @if ($buildingName)
                                                                                                <span
                                                                                                    class="font-normal text-slate-500">({{ $buildingName }})</span>
                                                                                            @endif
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <!-- Non-Teaching/Break Box -->
                                                                                <div
                                                                                    class="sm:col-span-2 lg:col-span-3 bg-white p-2 shadow-sm border border-slate-100">
                                                                                    <p
                                                                                        class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-0.5">
                                                                                        Keterangan</p>
                                                                                    <p
                                                                                        class="font-bold text-xs text-slate-800">
                                                                                        {{ $period?->name ?? 'Istirahat / Pembiasaan' }}
                                                                                    </p>
                                                                                    @if ($period?->description)
                                                                                        <p
                                                                                            class="text-[11px] text-slate-500 mt-0.5">
                                                                                            {{ $period->description }}
                                                                                        </p>
                                                                                    @endif
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Right Sidebar Menu -->
                <aside class="hidden xl:block xl:col-span-3 space-y-6">
                    <div class="sticky top-8">
                        <div class="p-5 bg-white border border-slate-200">
                            <h2 class="text-base font-bold text-slate-900 mb-3">Navigasi Hari</h2>
                            <div class="space-y-1">
                                @for ($d = 1; $d <= 7; $d++)
                                    @php
                                        $hasSchedule = isset($allSchedules[$d]) && $allSchedules[$d]->isNotEmpty();
                                        $isActive = $d === $currentDayIndex;
                                    @endphp
                                    <a href="#day-{{ $d }}"
                                        class="flex items-center justify-between px-3 py-2 border-l-4 transition-colors duration-150 
                                       {{ $isActive ? 'border-sky-500 bg-sky-50' : 'border-transparent hover:bg-slate-50 hover:border-slate-300' }} 
                                       {{ !$hasSchedule ? 'opacity-50' : '' }}"
                                        title="{{ !$hasSchedule ? 'Tidak ada jadwal' : '' }}">

                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-sm font-bold {{ $isActive ? 'text-sky-700' : 'text-slate-600' }}">{{ $dayNames[$d] }}</span>
                                        </div>

                                        @if (!$hasSchedule)
                                            <span
                                                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Libur</span>
                                        @elseif($isActive)
                                            <span class="w-1.5 h-1.5 bg-sky-500"></span>
                                        @endif
                                    </a>
                                @endfor
                            </div>
                        </div>
                    </div>
                </aside>

            </section>
        </main>
    </div>

    <!-- Mobile Floating Menu -->
    @if (!empty($allSchedules) && count($allSchedules) > 0)
        <div class="xl:hidden fixed bottom-4 right-4 z-50 flex items-end justify-end">
            <div class="relative">
                <button id="dayToggleBtn" aria-expanded="false" aria-controls="dayMenu"
                    class="bg-slate-900 p-3 shadow-xl text-white focus:outline-none border border-slate-700">
                    <x-heroicon-s-list-bullet class="w-6 h-6 text-white" />
                </button>
                <div id="dayMenu"
                    class="hidden absolute right-0 bottom-14 w-40 bg-white border border-slate-200 shadow-xl p-1.5">
                    <div
                        class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">
                        Pilih Hari
                    </div>
                    @for ($d = 1; $d <= 7; $d++)
                        @php
                            $hasSchedule = isset($allSchedules[$d]) && $allSchedules[$d]->isNotEmpty();
                            $isActive = $d === $currentDayIndex;
                        @endphp
                        <a href="#day-{{ $d }}"
                            onclick="document.getElementById('dayMenu').classList.add('hidden')"
                            class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50 {{ $isActive ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-600' }} {{ !$hasSchedule ? 'opacity-50' : '' }}"
                            title="{{ !$hasSchedule ? 'Tidak ada jadwal' : '' }}">
                            <span class="text-xs font-semibold flex-1">{{ $dayNames[$d] }}</span>
                            @if (!$hasSchedule)
                                <span class="text-[9px] uppercase font-bold text-slate-400">Libur</span>
                            @endif
                        </a>
                    @endfor
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('dayToggleBtn');
            var menu = document.getElementById('dayMenu');

            if (!btn || !menu) return;

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', !expanded);
                menu.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!menu.classList.contains('hidden') && !btn.contains(e.target)) {
                    menu.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>

</x-app-layout>
