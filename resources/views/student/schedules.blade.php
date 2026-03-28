<x-app-layout :title="'Jadwal Pelajaran - ' . ($dayNames[$currentDayIndex] ?? 'Jadwal Pelajaran')" :description="'Lihat jadwal pelajaran lengkap untuk setiap hari dalam seminggu, termasuk mata pelajaran, pengajar, kelas, dan ruangan.'">
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-900">
            Jadwal Pelajaran
        </h2>
    </x-slot>

    @php
        $currentTime = $currentTime ?? \Carbon\Carbon::now();
        $currentDayIndex = $currentDayIndex ?? (int) $currentTime->format('N');
        $dayNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu', 'Minggu'];
        $dayShort = ['', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $desktopTableCols = 'grid-cols-[96px_minmax(0,1.35fr)_minmax(0,1.1fr)_minmax(0,0.95fr)_minmax(0,1.1fr)]';
        $metaPillClass =
            'inline-flex items-center gap-[5px] border border-slate-200 bg-slate-100 px-[7px] py-[2px] text-[11px] font-medium text-slate-600';
        $pillIconClass = 'h-[11px] w-[11px] opacity-60';
        $ongoingBadgeClass =
            'mb-0.5 inline-flex w-fit items-center gap-1 bg-sky-500 px-1.5 py-0.5 text-[9px] font-bold tracking-[0.08em] text-white';
        $doneBadgeClass =
            'mb-0.5 inline-flex w-fit bg-slate-200 px-1.5 py-0.5 text-[9px] font-semibold tracking-[0.06em] text-slate-500';
    @endphp

    <div class="min-h-screen bg-slate-50 py-6">
        <div class="mx-auto max-w-7xl px-4 md:px-6">

            @if (empty($allSchedules) || count($allSchedules) === 0)
                <div class="border border-slate-200 bg-white p-12 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-slate-100 mb-4">
                        <span class="text-2xl">🗓️</span>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-1">Tidak Ada Jadwal</h4>
                    <p class="text-sm text-slate-500">Belum ada jadwal pelajaran yang tersedia.</p>
                </div>
            @else
                <div class="xl:flex xl:items-start xl:gap-6">
                    <aside
                        class="hidden xl:order-2 xl:block xl:w-72 xl:flex-shrink-0 xl:self-start xl:sticky xl:top-24 xl:max-h-[calc(100vh-7rem)] xl:overflow-y-auto">
                        <div class="space-y-4">
                            <div class="border border-slate-200 bg-white">
                                <div class="px-4 py-3 border-b border-slate-200">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Navigasi
                                    </p>
                                    <h3 class="text-sm font-semibold text-slate-900 mt-1">Jadwal per hari</h3>
                                </div>

                                <div class="flex flex-col">
                                    @for ($d = 1; $d <= 7; $d++)
                                        @php
                                            $hasSchedule = isset($allSchedules[$d]) && $allSchedules[$d]->isNotEmpty();
                                            $isActive = $d === $currentDayIndex;
                                            $scheduleCount = $hasSchedule ? $allSchedules[$d]->count() : 0;
                                        @endphp
                                        <a href="#day-{{ $d }}"
                                            class="flex items-center justify-between gap-3 px-3.5 py-3 text-[13px] font-semibold no-underline transition-all {{ $d > 1 ? 'border-t border-slate-100' : '' }} {{ $isActive ? 'border-l-[3px] border-sky-600 bg-sky-50 text-sky-600' : 'border-l-[3px] border-transparent text-slate-500 hover:border-slate-400 hover:bg-slate-50' }} {{ !$hasSchedule ? 'opacity-[0.55]' : '' }}">
                                            <span class="flex min-w-0 flex-col gap-0.5">
                                                <span
                                                    class="text-[13px] font-bold leading-tight {{ $isActive ? 'text-sky-600' : 'text-slate-900' }}">{{ $dayNames[$d] }}</span>
                                                <span
                                                    class="text-[10px] font-medium uppercase tracking-[0.08em] {{ $isActive ? 'text-sky-600' : 'text-slate-400' }}">{{ $hasSchedule ? $scheduleCount . ' sesi' : 'Libur' }}</span>
                                            </span>
                                            <span
                                                class="min-w-10 border px-2 py-1 text-center text-[10px] font-bold uppercase tracking-[0.08em] {{ $isActive ? 'border-sky-600 bg-sky-600 text-white' : 'border-slate-200 bg-slate-50 text-slate-500' }}">{{ $dayShort[$d] }}</span>
                                        </a>
                                    @endfor
                                </div>
                            </div>

                            <div class="border border-slate-200 bg-white p-4">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Ringkasan
                                </p>
                                <div class="mt-4 space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="text-xs text-slate-400 font-medium">Hari ini</span>
                                        <span
                                            class="text-sm font-semibold text-slate-800 text-right">{{ $dayNames[$currentDayIndex] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="text-xs text-slate-400 font-medium">Waktu</span>
                                        <span class="text-sm font-medium text-slate-700 text-right">
                                            {{ $currentTime->format('d M Y') }}
                                            <span class="text-slate-300">&bull;</span>
                                            {{ $currentTime->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <div class="xl:flex-1 xl:min-w-0 xl:order-1">

                        {{-- Day Tab Navigation --}}
                        <div class="hidden bg-white border border-slate-200 mb-6">
                            <div class="flex overflow-x-auto border-b border-slate-200">
                                @for ($d = 1; $d <= 7; $d++)
                                    @php
                                        $hasSchedule = isset($allSchedules[$d]) && $allSchedules[$d]->isNotEmpty();
                                        $isActive = $d === $currentDayIndex;
                                        $scheduleCount = $hasSchedule ? $allSchedules[$d]->count() : 0;
                                    @endphp
                                    <a href="#day-{{ $d }}"
                                        class="flex flex-col items-center gap-0.5 whitespace-nowrap px-3.5 py-1.5 text-[13px] font-semibold no-underline transition-all {{ $isActive ? 'border-b-2 border-sky-600 bg-sky-50 text-sky-600' : 'border-b-2 border-transparent text-slate-500 hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900' }} {{ !$hasSchedule ? 'opacity-40' : '' }}">
                                        <span>{{ $dayNames[$d] }}</span>
                                        @if ($hasSchedule)
                                            <span
                                                class="text-[10px] font-normal text-inherit opacity-70">{{ $scheduleCount }}
                                                sesi</span>
                                        @else
                                            <span class="text-[10px] font-normal opacity-50">Libur</span>
                                        @endif
                                    </a>
                                @endfor
                            </div>

                            {{-- Quick stats bar --}}
                            <div class="px-4 py-2.5 flex items-center gap-4 flex-wrap">
                                <span class="text-xs text-slate-400 font-medium">
                                    Hari ini:
                                    <span
                                        class="text-slate-700 font-semibold">{{ $dayNames[$currentDayIndex] ?? '-' }}</span>
                                </span>
                                <span class="text-slate-200 text-xs">|</span>
                                <span class="text-xs text-slate-400">
                                    {{ $currentTime->format('d M Y • H:i') }}
                                </span>
                            </div>
                        </div>

                        {{-- Schedule Sections --}}
                        @foreach ($allSchedules as $day => $schedules)
                            @php
                                $schedules = $schedules ?? collect();
                                $processedSchedules = [];

                                if ($schedules->isNotEmpty()) {
                                    foreach ($schedules->values() as $item) {
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

                            <div id="day-{{ $day }}" class="scroll-mt-28 mb-6">

                                {{-- Day Header --}}
                                <div
                                    class="sticky top-16 z-10 flex items-center gap-0 mb-0 bg-slate-50/95 backdrop-blur-sm">
                                    <div class="bg-slate-800 text-white px-4 py-2 flex items-center gap-3">
                                        <span
                                            class="text-sm font-bold tracking-wide">{{ $dayNames[$day] ?? 'Hari' }}</span>
                                        @if ($day === $currentDayIndex)
                                            <span
                                                class="bg-sky-500 text-white text-[9px] font-bold px-2 py-0.5 tracking-wider">HARI
                                                INI</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 h-px bg-slate-300"></div>
                                    <div class="bg-slate-100 border border-slate-200 px-3 py-1.5">
                                        <span
                                            class="text-xs text-slate-500 font-medium">{{ count($processedSchedules) }}
                                            sesi</span>
                                    </div>
                                </div>

                                {{-- Schedule Table --}}
                                <div class="border border-slate-200 bg-white border-t-0">
                                    @if (empty($processedSchedules))
                                        <div class="text-center py-8">
                                            <p class="text-xs font-semibold text-slate-400">Tidak ada jadwal untuk hari
                                                ini.</p>
                                        </div>
                                    @else
                                        {{-- Table Header --}}
                                        <div
                                            class="hidden sticky top-[6.3rem] z-[9] sm:grid {{ $desktopTableCols }} bg-slate-50 border-b border-slate-200">
                                            <div
                                                class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-r border-slate-200">
                                                Waktu</div>
                                            <div
                                                class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-r border-slate-200">
                                                Mata Pelajaran</div>
                                            <div
                                                class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-r border-slate-200">
                                                Pengajar</div>
                                            <div
                                                class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                                Ruangan</div>
                                        </div>

                                        @foreach ($processedSchedules as $schedule)
                                            @php
                                                $startTimeStr = $schedule->start_time ?? null;
                                                $endTimeStr = $schedule->end_time ?? null;
                                                $scheduleDay = $schedule->day_of_week ?? $day;

                                                $startCarbon = $startTimeStr
                                                    ? \Carbon\Carbon::parse($startTimeStr)
                                                    : null;
                                                $endCarbon = $endTimeStr ? \Carbon\Carbon::parse($endTimeStr) : null;

                                                $isOngoing = false;
                                                $isPast = false;

                                                if ((int) $scheduleDay === (int) $currentDayIndex) {
                                                    if ($startCarbon && $endCarbon && $currentTime) {
                                                        $isOngoing = $currentTime->between($startCarbon, $endCarbon);
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
                                                    data_get($schedule, 'subject.name') ?? 'Pembiasaan / Istirahat';
                                                $subjectCode = data_get($schedule, 'subject.code');

                                                $teacherName =
                                                    data_get($schedule, 'teacher.user.name') ??
                                                    (data_get($schedule, 'teacher.name') ?? '-');

                                                $teacherAvatar =
                                                    data_get($schedule, 'teacher.user.avatar') ??
                                                    (data_get($schedule, 'teacher.avatar') ??
                                                        data_get($schedule, 'teacher.avatar_url'));

                                                if ($teacherAvatar && filter_var($teacherAvatar, FILTER_VALIDATE_URL)) {
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
                                                $buildingName = $schedule->roomHistory?->room?->building?->name ?? null;
                                            @endphp

                                            {{-- Mobile Card --}}
                                            <div
                                                class="sm:hidden border-b border-slate-100 last:border-b-0 {{ !$isTeaching ? 'bg-amber-50' : ($isOngoing ? 'bg-sky-50' : ($isPast ? 'bg-slate-50 opacity-70' : 'bg-white')) }}">
                                                @if ($isOngoing)
                                                    <div
                                                        class="bg-sky-500 text-white text-[9px] font-bold px-3 py-1 tracking-widest flex items-center gap-1.5">
                                                        <span
                                                            class="inline-flex h-1.5 w-1.5 bg-white animate-ping"></span>
                                                        BERLANGSUNG
                                                    </div>
                                                @endif
                                                <div class="p-3 flex gap-3">
                                                    <div
                                                        class="flex-shrink-0 w-16 flex flex-col items-center justify-center {{ $isOngoing ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-700' }} p-2">
                                                        <span
                                                            class="text-base font-bold leading-none">{{ $formattedStart }}</span>
                                                        <span class="text-[10px] opacity-60 my-0.5">—</span>
                                                        <span
                                                            class="text-xs font-semibold leading-none">{{ $formattedEnd }}</span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        @if ($isTeaching)
                                                            <div class="flex items-center gap-1.5 mb-1.5">
                                                                <span
                                                                    class="font-bold text-sm text-slate-900 truncate">{{ $subjectName }}</span>
                                                                @if ($subjectCode)
                                                                    <span
                                                                        class="bg-sky-100 text-sky-700 text-[10px] font-bold px-1.5 py-0.5 flex-shrink-0">{{ $subjectCode }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="flex gap-1.5">
                                                                <div>
                                                                    <span class="{{ $metaPillClass }}">
                                                                        {{ $teacherName }}
                                                                    </span>
                                                                    <span class="{{ $metaPillClass }}">
                                                                        <x-heroicon-s-map-pin
                                                                            class="{{ $pillIconClass }}" />
                                                                        {{ $roomName }}{{ $buildingName ? " ({$buildingName})" : '' }}
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    <img src="{{ $teacherAvatarUrl }}"
                                                                        class="w-10 h-10 object-cover"
                                                                        onerror="this.src='{{ asset('images/default-teacher.png') }}'">
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span
                                                                class="font-bold text-sm text-amber-800">{{ $period?->name ?? 'Istirahat / Pembiasaan' }}</span>
                                                            @if ($period?->description)
                                                                <p class="text-xs text-amber-700 mt-0.5">
                                                                    {{ $period->description }}</p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Desktop Table Row --}}
                                            <div
                                                class="hidden sm:grid {{ $desktopTableCols }} min-h-[60px] border-b border-slate-100 last:border-b-0 relative
                                        {{ !$isTeaching ? 'bg-amber-50' : ($isOngoing ? 'bg-sky-50' : ($isPast ? 'opacity-60' : 'bg-white hover:bg-slate-50/60')) }}">

                                                @if ($isOngoing)
                                                    <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-sky-500"></div>
                                                @endif

                                                {{-- Time Cell --}}
                                                <div
                                                    class="border-r border-slate-200 flex flex-col items-center justify-center px-2 py-2 gap-0.5
                                            {{ $isOngoing ? 'bg-sky-500' : (!$isTeaching ? 'bg-amber-100' : 'bg-slate-50') }}">
                                                    <span
                                                        class="text-sm font-bold leading-none {{ $isOngoing ? 'text-white' : 'text-slate-800' }}">{{ $formattedStart }}</span>
                                                    <span
                                                        class="text-[9px] {{ $isOngoing ? 'text-sky-100' : 'text-slate-400' }}">s.d.</span>
                                                    <span
                                                        class="text-xs font-semibold {{ $isOngoing ? 'text-white' : 'text-slate-700' }}">{{ $formattedEnd }}</span>
                                                </div>

                                                @if ($isTeaching)
                                                    {{-- Subject --}}
                                                    <div
                                                        class="min-w-0 border-r border-slate-100 px-3 py-2 flex flex-col justify-center gap-1">
                                                        @if ($isOngoing)
                                                            <div class="{{ $ongoingBadgeClass }}">
                                                                <span
                                                                    class="inline-flex h-1.5 w-1.5 bg-white animate-ping"></span>
                                                                BERLANGSUNG
                                                            </div>
                                                        @elseif ($isPast)
                                                            <div class="{{ $doneBadgeClass }}">SELESAI</div>
                                                        @endif
                                                        <div class="flex items-center gap-1.5 min-w-0">
                                                            <span
                                                                class="font-bold text-sm text-slate-900 leading-tight truncate">{{ $subjectName }}</span>
                                                            @if ($subjectCode)
                                                                <span
                                                                    class="bg-sky-100 text-sky-700 text-[10px] font-bold px-1.5 py-0.5">{{ $subjectCode }}</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Teacher --}}
                                                    <div
                                                        class="min-w-0 border-r border-slate-100 px-3 py-2 flex items-center gap-2">
                                                        <img src="{{ $teacherAvatarUrl }}" alt="Guru"
                                                            class="w-7 h-7 object-cover border border-slate-200 flex-shrink-0"
                                                            onerror="this.src='{{ asset('images/default-teacher.png') }}'">
                                                        <span
                                                            class="min-w-0 text-xs text-slate-700 font-medium leading-tight line-clamp-2">{{ $teacherName }}</span>
                                                    </div>

                                                    {{-- Room --}}
                                                    <div class="min-w-0 px-3 py-2 flex items-center gap-2">
                                                        <div
                                                            class="w-6 h-6 bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                                                            <x-heroicon-s-map-pin class="w-3.5 h-3.5 text-slate-500" />
                                                        </div>
                                                        <div class="min-w-0">
                                                            <span
                                                                class="text-xs text-slate-700 font-medium truncate block">{{ $roomName }}</span>
                                                            @if ($buildingName)
                                                                <span
                                                                    class="text-[10px] text-slate-400">{{ $buildingName }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    {{-- Non-Teaching / Break — spans remaining 4 columns --}}
                                                    <div class="col-span-4 min-w-0 px-3 py-2 flex items-center gap-3">
                                                        <span
                                                            class="w-1 self-stretch bg-amber-400 flex-shrink-0"></span>
                                                        <div class="min-w-0">
                                                            <span
                                                                class="font-bold text-sm text-amber-800">{{ $period?->name ?? $period->ordinal }}</span>
                                                            @if ($period?->description)
                                                                <p class="text-xs text-amber-600 mt-0.5 mb-0">
                                                                    {{ $period->description }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            @endif
            </main>
        </div>

        {{-- Mobile Floating Day Menu --}}
        @if (!empty($allSchedules) && count($allSchedules) > 0)
            <div class="xl:hidden fixed bottom-4 right-4 z-50">
                <div class="relative">
                    <button id="dayToggleBtn" aria-expanded="false" aria-controls="dayMenu"
                        class="bg-slate-900 p-3 shadow-xl text-white focus:outline-none border border-slate-700">
                        <x-heroicon-s-list-bullet class="w-5 h-5 text-white" />
                    </button>
                    <div id="dayMenu"
                        class="hidden absolute right-0 bottom-14 w-44 bg-white border border-slate-200 shadow-xl">
                        <div
                            class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            Pilih Hari
                        </div>
                        @for ($d = 1; $d <= 7; $d++)
                            @php
                                $hasSchedule = isset($allSchedules[$d]) && $allSchedules[$d]->isNotEmpty();
                                $isActive = $d === $currentDayIndex;
                            @endphp
                            <a href="#day-{{ $d }}"
                                onclick="document.getElementById('dayMenu').classList.add('hidden'); document.getElementById('dayToggleBtn').setAttribute('aria-expanded','false');"
                                class="flex items-center justify-between px-3 py-2 border-b border-slate-50 last:border-b-0 hover:bg-slate-50
                            {{ $isActive ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-600' }}
                            {{ !$hasSchedule ? 'opacity-40' : '' }}">
                                <span class="text-xs font-semibold">{{ $dayNames[$d] }}</span>
                                @if (!$hasSchedule)
                                    <span class="text-[9px] uppercase font-bold text-slate-400">Libur</span>
                                @elseif ($isActive)
                                    <span class="w-1.5 h-1.5 bg-sky-500"></span>
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

                // Smooth scroll to today's section on load
                var todayEl = document.getElementById('day-{{ $currentDayIndex }}');
                if (todayEl) {
                    setTimeout(function() {
                        todayEl.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 200);
                }
            });
        </script>

</x-app-layout>
