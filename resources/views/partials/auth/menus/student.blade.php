@php
    $sectionTitleClass = 'px-3 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400  mb-1';
    $linkClass =
        'group flex items-center gap-2.5 xl px-3 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900   ';
    $activeClass = 'bg-slate-200 text-slate-900 shadow-sm';
@endphp

<div class="space-y-4">
    <div class="space-y-0.5">
        <p class="{{ $sectionTitleClass }}">Akademik</p>
        <a href="{{ route('siswa.index') }}"
            class="{{ $linkClass }} {{ request()->routeIs('siswa.index') ? $activeClass : '' }}">
            <x-heroicon-o-home class="h-5 w-5  mr-3 group-hover:translate-x-1 duration-300" />
            Dashboard
        </a>
        <a href="{{ route('siswa.schedules.index') }}"
            class="{{ $linkClass }} {{ request()->routeIs('siswa.schedules.*') ? $activeClass : '' }}">
            <x-heroicon-o-calendar class="h-5 w-5  mr-3 group-hover:translate-x-1 duration-300" />
            Jadwal Pelajaran
        </a>
        <a href="{{ route('siswa.classroom.index') }}"
            class="{{ $linkClass }} {{ request()->routeIs('siswa.classroom.*') ? $activeClass : '' }}">
            <x-heroicon-c-queue-list class="h-5 w-5  mr-3 group-hover:translate-x-1 duration-300" />
            Kelas Saya
        </a>
    </div>

    {{-- // simpan ke paling bawah menu akun karena lebih jarang diakses --}}
    <div class="pt-4 mt-4 border-t border-slate-200 space-y-0.5">
        <p class="{{ $sectionTitleClass }}">Akun</p>
        <a href="{{ route('siswa.profile.index') }}"
            class="{{ $linkClass }} {{ request()->routeIs('siswa.profile.*') ? $activeClass : '' }}">
            <x-heroicon-o-user class="h-5 w-5  mr-3 group-hover:translate-x-1 duration-300" />
            Profil Saya
        </a>
    </div>
</div>
