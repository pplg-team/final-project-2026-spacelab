<x-app-layout :title="'Ruang Kelas Saya - ' . ($classroom?->full_name ?? 'Tidak Diketahui')" :description="'Lihat detail ruang kelas, wali kelas, dan daftar siswa dalam kelas.'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Ruang Kelas Saya') }}
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen bg-slate-50">
        <!-- Class Header -->
        <section
            class="p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white border border-slate-200">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-primary border border-slate-200 flex items-center justify-center text-primary">
                    <x-heroicon-o-academic-cap class="w-10 h-10" />
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ $classroom?->full_name ?? 'Belum Ada Kelas' }}</h1>
                    <p class="text-sky-300 font-medium">
                        {{ $classroom?->major?->name ?? ($classroom?->major?->code ?? 'Jurusan Tidak Diketahui') }}
                    </p>
                    <div class="mt-2 flex items-center gap-2 text-on-surface-variant text-sm">
                        <x-heroicon-o-map-pin class="w-4 h-4 text-slate-400" />
                        {{ $classroom?->building?->name ?? 'Lokasi belum tersedia' }}
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4 bg-white p-4 border border-slate-200">
                <div class="text-right">
                    <p class="text-xs uppercase tracking-widest text-slate-400 font-semibold">Wali Kelas</p>
                    <p class="text-on-surface font-semibold">
                        {{ $guardian?->teacher?->user?->name ?? 'Belum ditugaskan' }}</p>
                </div>
                <div class="w-12 h-12 border-2 border-primary/30 p-0.5">
                    <img class="w-full h-full object-cover" alt="Foto profil wali kelas"
                        src="{{ $guardian?->teacher?->avatar_url ?? asset('assets/images/avatar/default-profile.png') }}" />
                </div>
            </div>
        </section>
        <!-- Stats Grid -->
        @php
            $studentCollection =
                $students instanceof \Illuminate\Pagination\AbstractPaginator
                    ? $students->getCollection()
                    : collect($students);
            $femaleStudents = $studentCollection
                ->filter(fn($student) => optional($student->user)->gender === 'female')
                ->count();
            $maleStudents = $studentCollection
                ->filter(fn($student) => optional($student->user)->gender === 'male')
                ->count();
            $attendanceRate = $totalStudents > 0 ? round(($todayEntries->count() / $totalStudents) * 100) : 0;
        @endphp
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 py-5">
            <div class="p-6 flex items-center gap-4 border border-slate-200 bg-white">
                <div class="w-12 h-12 bg-sky-400 flex items-center justify-center text-sky-300">
                    <x-heroicon-o-user-group class="w-6 h-6 text-white" />
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wider">Jumlah Siswa</p>
                    <p class="text-2xl font-bold">{{ $totalStudents }}</p>
                </div>
            </div>
            <div class="p-6 flex items-center gap-4 border border-slate-200 bg-white">
                <div class="w-12 h-12 bg-pink-400 flex items-center justify-center text-pink-300">
                    <x-heroicon-s-user-circle class="w-6 h-6 text-white" />
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wider">Perempuan</p>
                    <p class="text-2xl font-bold">{{ $femaleStudents }}</p>
                </div>
            </div>
            <div class="p-6 flex items-center gap-4 border border-slate-200 bg-white">
                <div class="w-12 h-12 bg-blue-400 flex items-center justify-center text-blue-300">
                    <x-heroicon-s-user-circle class="w-6 h-6 text-white" />
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wider">Laki-laki</p>
                    <p class="text-2xl font-bold">{{ $maleStudents }}</p>
                </div>
            </div>
            <div class="p-6 flex items-center gap-4 border border-slate-200 bg-white">
                <div class="w-12 h-12 bg-emerald-400 flex items-center justify-center text-emerald-300">
                    <x-heroicon-s-check-circle class="w-6 h-6 text-white" />
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wider">Kehadiran Hari Ini</p>
                    <p class="text-2xl font-bold">{{ $attendanceRate }}%</p>
                </div>
            </div>
        </section>
        <!-- Layout Split -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Student List (Main) -->
            <div class="lg:col-span-2 space-y-2 border border-slate-200 bg-white p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold">Daftar Siswa</h3>
                    <div class="flex gap-2">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                                <x-heroicon-s-magnifying-glass class="w-4 h-4" />
                            </span>
                            <input
                                class="bg-surface-container border border-slate-200 pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary/50 w-64 transition-all"
                                placeholder="Cari siswa..." type="text" />
                        </div>
                        <button class="px-4 py-2 text-sm flex items-center gap-2 hover:bg-white/10 transition-colors">
                            <x-heroicon-o-funnel class="w-4 h-4 text-slate-400" />
                            Filter
                        </button>
                    </div>
                </div>
                <div class="overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-slate-200">
                            <tr>
                                <th class="font-semibold text-slate-300">No</th>
                                <th class="font-semibold text-slate-300">Nama Siswa</th>
                                <th class="font-semibold text-slate-300">NIS</th>
                                <th class="font-semibold text-slate-300 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($students as $index => $student)
                                <tr class="group">
                                    <td class="text-slate-400">
                                        {{ str_pad(($students->firstItem() ?? 0) + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-surface-container-highest flex items-center justify-center overflow-hidden border border-white/10">
                                                <img class="w-full h-full object-cover"
                                                    alt="Foto siswa {{ $student->user->name ?? 'Tanpa Nama' }}"
                                                    src="{{ $student->avatar ?? asset('assets/images/avatar/default-profile.png') }}" />
                                            </div>
                                            <div>
                                                <p class="font-medium text-on-surface">
                                                    {{ $student->user->name ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-slate-400">{{ $student->nis ?? '-' }}</td>
                                    <td class="text-right">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium bg-emerald-400/10 text-emerald-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Hadir
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-400">Belum ada siswa
                                        dalam kelas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end">
                    {{ $students->links() }}
                </div>
            </div>
            <!-- Sidebar -->
            <aside class="space-y-8">
                <!-- Pelajaran Saat Ini -->
                @php
                    $nextEntry = null;
                    if (!$currentEntry && $todayEntries->isNotEmpty()) {
                        $nextEntry = $todayEntries->first(
                            fn($entry) => !method_exists($entry, 'isPast') || !$entry->isPast($currentTime),
                        );
                    }
                @endphp
                <section class="space-y-4 border border-slate-200 bg-white p-6">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <x-heroicon-s-clock class="w-5 h-5 text-sky-300" />
                        Pelajaran Saat Ini
                    </h3>
                    <div class="glass-elevated p-6 border-l-4 border-l-primary relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 -mr-16 -mt-16 blur-3xl"></div>
                        <p class="text-slate-400 text-sm mb-1">Status Sesi</p>
                        @if ($currentEntry)
                            <p class="text-on-surface font-medium">
                                {{ optional($currentEntry->teacherSubject->subject)->name ?? 'Sesi berjalan' }}
                                oleh
                                {{ optional($currentEntry->teacherSubject->teacher->user)->name ?? 'Pengajar belum ditetapkan' }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ optional($currentEntry->period)->start_time ?? '-' }} -
                                {{ optional($currentEntry->period)->end_time ?? '-' }}</p>
                        @else
                            <p class="text-on-surface font-medium italic">Tidak ada pelajaran sedang berlangsung</p>
                            <p class="text-xs text-slate-400">
                                {{ $nextEntry ? 'Berikutnya: ' . (optional($nextEntry->period)->start_time ?? '-') . ' - ' . (optional($nextEntry->period)->end_time ?? '-') . ' (' . (optional($nextEntry->teacherSubject->subject)->name ?? '-') . ')' : 'Tidak ada jadwal lagi hari ini' }}
                            </p>
                        @endif
                        <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                            <span
                                class="text-xs text-slate-500">{{ $currentTime->translatedFormat('l, d F Y') }}</span>
                        </div>
                    </div>
                </section>
                <!-- Jadwal Hari Ini -->
                <section class="space-y-4 border border-slate-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-5 h-5 text-sky-300" />
                            Jadwal Hari Ini
                        </h3>
                        <span class="text-xs text-slate-500">{{ $currentTime->translatedFormat('l, d F') }}</span>
                    </div>
                    <div class="space-y-3">
                        @forelse($todayEntries as $entry)
                            @php
                                $period = optional($entry->period);
                                $subject = optional(optional($entry->teacherSubject)->subject);
                                $teacher = optional(optional($entry->teacherSubject)->teacher->user);
                                $isOngoing = method_exists($entry, 'isOngoing') && $entry->isOngoing($currentTime);
                                $isPast = method_exists($entry, 'isPast') && $entry->isPast($currentTime);
                            @endphp
                            <div
                                class="p-4 flex items-start gap-4 hover:bg-white/5 transition-all cursor-pointer {{ $isPast ? 'opacity-60' : '' }}">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs font-bold text-white">{{ $period->start_time ?? '-' }}</span>
                                    <div class="w-px h-8 bg-white/10 my-1"></div>
                                    <span class="text-xs text-slate-500">{{ $period->end_time ?? '-' }}</span>
                                </div>
                                <div class="flex-1">
                                    <h4
                                        class="text-sm font-semibold {{ $isOngoing ? 'text-emerald-300' : 'text-primary' }} transition-colors">
                                        {{ $subject->name ?? 'Mata Pelajaran tidak tersedia' }}</h4>
                                    <p class="text-xs text-slate-400">
                                        {{ $teacher->name ?? 'Pengajar belum ditetapkan' }}</p>
                                </div>
                                @if ($isOngoing)
                                    <x-heroicon-s-check-circle class="w-4 h-4 text-emerald-400" />
                                @elseif($isPast)
                                    <x-heroicon-o-clock class="w-4 h-4 text-slate-500" />
                                @else
                                    <x-heroicon-o-arrow-right class="w-4 h-4 text-blue-300" />
                                @endif
                            </div>
                        @empty
                            <div class="text-slate-400 text-sm">Belum ada jadwal untuk hari ini.</div>
                        @endforelse
                    </div>
                    <a href="{{ route('siswa.schedules.index') }}"
                        class="w-full py-3 text-sm font-medium text-center transition-colors border-dashed border-slate-200">Lihat
                        Jadwal Pelajaran</a>
                </section>
                <!-- Gender Distribution -->
                <section class="p-6 space-y-4 border border-slate-200 bg-white">
                    <h3 class="text-sm font-semibold uppercase tracking-widest">Distribusi Gender
                    </h3>
                    <div class="flex h-4 w-full bg-white/5 overflow-hidden border border-white/10">
                        <div class="bg-pink-400 w-[51%] h-full shadow-[0_0_15px_rgba(244,114,182,0.3)]"
                            title="Perempuan"></div>
                        <div class="bg-blue-400 w-[49%] h-full shadow-[0_0_15px_rgba(96,165,250,0.3)]"
                            title="Laki-laki"></div>
                    </div>
                    <div class="flex justify-between text-xs font-medium">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-pink-400"></div>
                            <span>51% Perempuan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-blue-400"></div>
                            <span>49% Laki-laki</span>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
