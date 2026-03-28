<x-app-layout :title="'Dashboard Siswa - ' . Auth::user()->name" :description="'Lihat ringkasan jadwal, absensi, dan informasi penting lainnya untuk hari ini.'">
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-900 ">
            Dashboard Siswa
        </h2>
    </x-slot>

    <!-- Floating Absensi -->
    @if ($attendanceRecord)
        <div class="fixed right-4 bottom-4 w-full max-w-md z-50">
            <div class="bg-white  shadow-xl border border-slate-200  p-5">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-red-100  flex items-center justify-center">
                        <x-heroicon-s-calendar class="w-8 h-8 text-red-600 " />
                    </div>

                    <div class="flex-1">
                        <p class="text-xs font-bold text-blue-600  uppercase">
                            Absensi Hari Ini
                        </p>

                        @if (!$isAbsensiActive)
                            <h3 class="mt-1 text-xl font-bold text-slate-500">
                                Belum Dibuka
                            </h3>
                        @elseif($attendanceRecord)
                            <h3 class="mt-1 text-xl font-bold text-green-600">
                                Sudah Absen
                            </h3>
                        @else
                            <h3 class="mt-1 text-xl font-bold text-red-600">
                                Belum Absen
                            </h3>
                        @endif

                        @if ($isAbsensiActive && !$attendanceRecord)
                            <a href="{{ route('student.attendance.show', $activeSessionToken) }}"
                                class="inline-flex items-center gap-1 mt-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold">
                                Absen Sekarang
                                <x-heroicon-s-arrow-right class="w-4 h-4" />
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="py-10 min-h-screen bg-slate-50 ">
        <main class="mx-auto max-w-7xl px-4 md:px-10 space-y-10">

            <!-- Header -->
            <header class="flex flex-col md:flex-row justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-slate-900 ">
                        Halo, {{ Auth::user()->name }}
                    </h1>
                    <p class="mt-1 text-sm text-slate-500  flex items-center gap-2">
                        <x-heroicon-s-calendar class="w-4 h-4" />
                        {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }} •
                        {{ $studentClassFullName }}
                    </p>
                </div>
                <div>
                    {{-- Profile Picture --}}
                    <img src="{{ Auth::user()->student->avatar_url }}"
                        alt="Foto Profil {{ Auth::user()->student->name }}"
                        class="w-16 h-16 object-cover border-2 border-slate-300 ">
                </div>
            </header>

            <!-- Stats -->
            <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <article class="p-5 bg-white border border-slate-200  shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-sky-50  flex items-center justify-center">
                            <x-heroicon-s-book-open class="w-5 h-5 text-sky-500" />
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 ">
                        Total Pelajaran Hari Ini
                    </p>
                    <p class="text-3xl font-extrabold text-slate-900 ">
                        {{ $countToday }}
                    </p>
                </article>

                <article class="p-5 bg-white  border border-slate-200  shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-indigo-50  flex items-center justify-center">
                            <x-heroicon-s-clock class="w-5 h-5 text-indigo-500" />
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 ">
                        Waktu Sekarang
                    </p>
                    <p class="text-2xl font-bold text-slate-900 ">
                        {{ $currentTime->format('H:i') }}
                    </p>
                </article>

                {{-- statistik absensi berupa streak, achievement dan jumlah (hardcode) --}}
                <article class="p-5 bg-white  border border-slate-200  shadow-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div
                                    class="w-10 h-10 bg-orange-50  flex items-center justify-center">
                                    <x-heroicon-s-fire class="w-5 h-5 text-orange-500" />
                                </div>
                            </div>
                            <p class="text-sm text-slate-500 ">
                                Streak Absensi
                            </p>
                            <p class="text-2xl font-bold text-slate-900 ">
                                5 Hari
                            </p>
                        </div>
                        <div class="pt-4 sm:pt-0 sm:pl-4 sm:border-l border-slate-200 ">
                            <div class="flex items-center justify-between mb-3">
                                <div
                                    class="w-10 h-10 bg-yellow-50  flex items-center justify-center">
                                    <x-heroicon-s-trophy class="w-5 h-5 text-yellow-500" />
                                </div>
                            </div>
                            <p class="text-sm text-slate-500 ">
                                Pencapaian Absensi
                            </p>
                            <p class="text-2xl font-bold text-slate-900 ">
                                3 Pencapaian
                            </p>
                        </div>
                    </div>
                </article>
            </section>

            <!-- Jadwal -->
            <section class="grid grid-cols-1 xl:grid-cols-12 gap-8">

                <div class="xl:col-span-8 space-y-6 ">
                    <h2 class="text-2xl font-bold text-slate-900 border-b border-slate-200  pb-2">
                        Jadwal Hari Ini
                    </h2>

                    <div class="bg-white  shadow-sm border border-slate-200">
                        <div class="p-6">
                            @if ($schedulesToday->isEmpty())
                                <div class="text-center py-12">
                                    <div
                                        class="inline-flex items-center justify-center w-20 h-20 bg-slate-100  mb-5 shadow-sm">
                                        <span class="text-4xl">🎉</span>
                                    </div>
                                    <h4 class="text-xl font-bold text-slate-900  mb-2">
                                        Tidak ada pelajaran hari ini
                                    </h4>
                                    <p class="text-sm text-slate-500 ">
                                        Nikmati hari liburmu dengan baik!
                                    </p>
                                </div>
                            @else
                                <div class="space-y-4 max-h-[720px] overflow-y-auto pr-2">
                                    @php
                                        $currentTime = $currentTime ?? \Carbon\Carbon::now();
                                        $currentDayIndex = $currentDayIndex ?? (int) $currentTime->format('N');
                                    @endphp

                                    @foreach ($schedulesToday as $schedule)
                                        @php
                                            $period = $schedule->period;
                                            $startTime = $period?->start_time;
                                            $endTime = $period?->end_time;
                                            $formattedStart = $startTime
                                                ? \Carbon\Carbon::parse($startTime)->format('H:i')
                                                : $period?->start_date?->format('H:i') ?? '-';
                                            $formattedEnd = $endTime
                                                ? \Carbon\Carbon::parse($endTime)->format('H:i')
                                                : $period?->end_date?->format('H:i') ?? '-';
                                            $hasDay = !is_null(data_get($schedule, 'day_of_week'));
                                            $isOngoing =
                                                ($hasDay
                                                    ? (int) data_get($schedule, 'day_of_week') ===
                                                        (int) $currentDayIndex
                                                    : true) && ($schedule->isOngoing($currentTime) ?? false);
                                            $isPast =
                                                ($hasDay
                                                    ? (int) data_get($schedule, 'day_of_week') ===
                                                        (int) $currentDayIndex
                                                    : true) && ($schedule->isPast($currentTime) ?? false);
                                            $isTeaching =
                                                $period?->is_teaching ??
                                                (bool) (data_get($schedule, 'teacherSubject.subject') ??
                                                    data_get($schedule, 'subject'));
                                            $subjectName =
                                                data_get($schedule, 'teacherSubject.subject.name') ??
                                                (data_get($schedule, 'subject.name') ?? 'Istirahat');
                                            $subjectCode =
                                                data_get($schedule, 'teacherSubject.subject.code') ??
                                                data_get($schedule, 'subject.code');
                                            $teacherName =
                                                data_get($schedule, 'teacherSubject.teacher.user.name') ??
                                                (data_get($schedule, 'teacher.user.name') ??
                                                    (data_get($schedule, 'teacher.name') ?? '-'));
                                            $className =
                                                data_get($schedule, 'template.class.full_name') ??
                                                ($studentClassFullName ?? '-');
                                            $roomName = data_get($schedule, 'roomHistory.room.name') ?? '-';
                                            $buildingName = data_get($schedule, 'roomHistory.room.building.name');
                                            $teacherAvatar =
                                                data_get($schedule, 'teacherSubject.teacher.avatar') ??
                                                data_get($schedule, 'teacher.avatar');
                                            $teacherAvatarUrl = $teacherAvatar
                                                ? Storage::url($teacherAvatar)
                                                : asset('images/default-teacher.png');
                                        @endphp

                                        <div class="relative group">
                                            @if ($isOngoing)
                                                <div
                                                    class="absolute -left-3 top-0 bottom-0 w-1 h-full bg-sky-400 shadow animate-pulse">
                                                </div>
                                            @endif

                                            @php
                                                $cardClasses = '';
                                                if (!$isTeaching) {
                                                    $cardClasses =
                                                        'bg-amber-50  border-amber-200 ';
                                                } elseif ($isOngoing) {
                                                    $cardClasses =
                                                        'bg-sky-50  border-sky-200  shadow-sm';
                                                } elseif ($isPast) {
                                                    $cardClasses =
                                                        'bg-slate-50  border-slate-200  opacity-80';
                                                } else {
                                                    $cardClasses =
                                                        'bg-white  border-slate-200  hover:shadow-sm';
                                                }
                                            @endphp

                                            <div class="relative overflow-hidden border {{ $cardClasses }}">
                                                @if ($isOngoing)
                                                    <div
                                                        class="absolute top-0 right-0 bg-sky-500 text-white px-3 py-1.5-xl shadow flex items-center gap-2">
                                                        <span class="relative flex h-2.5 w-2.5">
                                                            <span
                                                                class="animate-ping absolute inline-flex h-full w-full bg-white opacity-75"></span>
                                                            <span
                                                                class="relative inline-flex h-2.5 w-2.5 bg-white"></span>
                                                        </span>
                                                        <span
                                                            class="text-[10px] font-bold tracking-wide">BERLANGSUNG</span>
                                                    </div>
                                                @endif

                                                @if ($isPast)
                                                    <div
                                                        class="absolute top-3 right-3 bg-slate-200  text-slate-700  px-3 py-1 shadow-sm">
                                                        <span class="text-[10px] font-semibold">SELESAI</span>
                                                    </div>
                                                @endif

                                                <div class="p-5">
                                                    <div class="flex flex-col lg:flex-row gap-5">
                                                        <div class="flex-shrink-0">
                                                            <div
                                                                class="inline-flex flex-col items-center justify-center p-4 min-w-[100px] shadow-sm {{ $isOngoing ? 'bg-sky-500 text-white ring-2 ring-sky-200 ' : 'bg-slate-100  text-slate-900 ' }}">
                                                                <div class="text-3xl font-bold leading-none">
                                                                    {{ $formattedStart }}
                                                                </div>
                                                                <div class="text-xs opacity-90 mt-1">s.d.</div>
                                                                <div class="text-lg font-semibold leading-none">
                                                                    {{ $formattedEnd }}
                                                                </div>
                                                                @if ($period?->ordinal)
                                                                    <div
                                                                        class="mt-2.5 px-3 py-1 bg-white/25 backdrop-blur-sm">
                                                                        <span class="text-xs font-bold">Jam
                                                                            {{ $period->ordinal }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="flex-1 min-w-0">
                                                            <div class="mb-4">
                                                                @if (!$isTeaching)
                                                                    <div class="flex items-center gap-3">
                                                                        <h4
                                                                            class="text-xl font-bold text-slate-900  leading-tight">
                                                                            {{ $period?->ordinal ?? 'Pembiasaan' }}
                                                                        </h4>
                                                                    </div>
                                                                @else
                                                                    <h4
                                                                        class="text-xl font-bold text-slate-900  leading-tight">
                                                                        {{ $subjectName }}
                                                                    </h4>
                                                                    @if ($subjectCode)
                                                                        <span
                                                                            class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-sky-100 text-sky-700  ">
                                                                            {{ $subjectCode }}
                                                                        </span>
                                                                    @endif
                                                                @endif
                                                            </div>

                                                            <div class="grid md:grid-cols-3 gap-4">
                                                                @if ($isTeaching)
                                                                    <div
                                                                        class="flex items-center gap-3 bg-white  p-3 shadow-sm border border-slate-100 ">
                                                                        <img src="{{ $teacherAvatarUrl }}"
                                                                            alt="Guru"
                                                                            class="w-11 h-11 object-cover border-2 shadow {{ $isOngoing ? 'border-sky-300 ring-2 ring-sky-100 ' : 'border-slate-200 ' }}">
                                                                        <div class="flex-1 min-w-0">
                                                                            <p
                                                                                class="text-xs font-medium text-slate-500  mb-0.5">
                                                                                Pengajar</p>
                                                                            <p
                                                                                class="font-semibold text-sm text-slate-900  truncate">
                                                                                {{ $teacherName }}
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="flex items-center gap-3 bg-white  p-3 shadow-sm border border-slate-100 ">
                                                                        <div
                                                                            class="flex-shrink-0 w-11 h-11 bg-slate-200  flex items-center justify-center shadow">
                                                                            <x-heroicon-o-user-group
                                                                                class="h-5 w-5 text-slate-500 " />
                                                                        </div>
                                                                        <div class="flex-1 min-w-0">
                                                                            <p
                                                                                class="text-xs font-medium text-slate-500  mb-0.5">
                                                                                Kelas</p>
                                                                            <p
                                                                                class="font-semibold text-sm text-slate-900  truncate">
                                                                                {{ $className }}
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="flex items-center gap-3 bg-white  p-3 shadow-sm border border-slate-100 ">
                                                                        <div
                                                                            class="flex-shrink-0 w-11 h-11 bg-slate-200  flex items-center justify-center shadow">
                                                                            <x-heroicon-o-map-pin
                                                                                class="w-5 h-5 text-slate-600 " />
                                                                        </div>
                                                                        <div class="flex-1 min-w-0">
                                                                            <p
                                                                                class="text-xs font-medium text-slate-500  mb-0.5">
                                                                                Ruangan</p>
                                                                            <p
                                                                                class="font-semibold text-sm text-slate-900  truncate">
                                                                                {{ $roomName }}
                                                                            </p>
                                                                            @if ($buildingName)
                                                                                <p
                                                                                    class="text-xs text-slate-500  truncate">
                                                                                    {{ $buildingName }}
                                                                                </p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div
                                                                        class="md:col-span-3 bg-white  p-3 shadow-sm border border-slate-100 ">
                                                                        <p
                                                                            class="text-xs font-medium text-slate-500  mb-1">
                                                                            Jenis</p>
                                                                        <p
                                                                            class="font-semibold text-sm text-slate-900 ">
                                                                            {{ $period?->ordinal ?? 'Pembiasaan' }}
                                                                        </p>
                                                                        @if ($period?->description)
                                                                            <p
                                                                                class="text-sm text-slate-500  mt-2">
                                                                                {{ $period->description }}</p>
                                                                        @endif
                                                                        @if ($roomName !== '-')
                                                                            <div
                                                                                class="mt-3 text-xs text-slate-500 ">
                                                                                Lokasi:
                                                                                {{ $roomName }}
                                                                            </div>
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

                <!-- Sidebar -->
                <aside class="xl:col-span-4 space-y-6">
                    <div class="p-6 bg-white border border-slate-200 ">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-slate-900 ">Absensi &amp; Konsistensi</h2>
                            <span
                                class="text-xs font-bold text-sky-500  uppercase">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }}</span>
                        </div>
                        <!-- Simple Calendar View -->
                        <div class="grid grid-cols-7 gap-2 mb-6">
                            <div class="text-[10px] font-bold text-center text-slate-400 uppercase">Sn</div>
                            <div class="text-[10px] font-bold text-center text-slate-400 uppercase">Sl</div>
                            <div class="text-[10px] font-bold text-center text-slate-400 uppercase">Rb</div>
                            <div class="text-[10px] font-bold text-center text-slate-400 uppercase">Km</div>
                            <div class="text-[10px] font-bold text-center text-slate-400 uppercase">Jm</div>
                            <div class="text-[10px] font-bold text-center text-slate-400 uppercase">Sb</div>
                            <div class="text-[10px] font-bold text-center text-slate-400 uppercase">Mg</div>
                            <!-- Empty slots or prev month -->
                            <div class="aspect-square flex items-center justify-center text-[10px] text-slate-300">27
                            </div>
                            <div class="aspect-square flex items-center justify-center text-[10px] text-slate-300">28
                            </div>
                            <div class="aspect-square flex items-center justify-center text-[10px] text-slate-300">29
                            </div>
                            <div class="aspect-square flex items-center justify-center text-[10px] text-slate-300">30
                            </div>
                            <div class="aspect-square flex items-center justify-center text-[10px] text-slate-300">31
                            </div>
                            <!-- Current Month Days -->
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-green-500/15 text-green-600  border border-green-500/30">
                                1</div>
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-green-500/15 text-green-600  border border-green-500/30">
                                2</div>
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-green-500/15 text-green-600  border border-green-500/30">
                                3</div>
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-green-500/15 text-green-600  border border-green-500/30">
                                4</div>
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-red-500/10 text-red-600  border border-red-500/30">
                                5</div>
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-green-500/15 text-green-600  border border-green-500/30">
                                6</div>
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-green-500/15 text-green-600  border border-green-500/30">
                                7</div>
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-green-500/15 text-green-600  border border-green-500/30">
                                8</div>
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-green-500/15 text-green-600  border border-green-500/30">
                                9</div>
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-green-500/15 text-green-600  border border-green-500/30">
                                10</div>
                            <div
                                class="aspect-square flex items-center justify-center text-xs font-bold bg-sky-500 text-white ring-2 ring-sky-400/60 ring-offset-2 ring-offset-white ">
                                11</div>
                            <div class="aspect-square flex items-center justify-center text-xs text-slate-400">12</div>
                            <div class="aspect-square flex items-center justify-center text-xs text-slate-400">13</div>
                            <div class="aspect-square flex items-center justify-center text-xs text-slate-400">14</div>
                        </div>
                        <div class="space-y-4">
                            <div
                                class="flex items-center justify-between p-3 bg-slate-50  border border-slate-200 ">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-10 bg-green-500"></div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-500 uppercase">Hadir</p>
                                        <p class="text-xl font-bold text-slate-900 ">96%</p>
                                    </div>
                                </div>
                                <div class="w-16 h-8 flex items-end gap-0.5">
                                    <div class="flex-1 bg-green-500/40 h-[60%]"></div>
                                    <div class="flex-1 bg-green-500/40 h-[80%]"></div>
                                    <div class="flex-1 bg-green-500 h-[100%]"></div>
                                    <div class="flex-1 bg-green-500/40 h-[90%]"></div>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between p-3 bg-slate-50  border border-slate-200 ">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-10 bg-red-500"></div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-500 uppercase">Izin/Alpha</p>
                                        <p class="text-xl font-bold text-slate-900 ">4%</p>
                                    </div>
                                </div>
                                <div class="w-16 h-8 flex items-end gap-0.5">
                                    <div class="flex-1 bg-red-500/20 h-[20%]"></div>
                                    <div class="flex-1 bg-red-500 h-[40%]"></div>
                                    <div class="flex-1 bg-red-500/20 h-[10%]"></div>
                                    <div class="flex-1 bg-red-500/20 h-[15%]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

            </section>

        </main>
    </div>
</x-app-layout>
