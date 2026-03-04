<x-app-layout title="Pantau Ruangan" description="Monitor CCTV ruangan per jurusan secara real-time">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <x-heroicon-o-video-camera class="w-5 h-5 text-gray-600 dark:text-gray-300" />
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pantau Ruangan
            </h2>
        </div>
    </x-slot>

    @php
        // Ambil semua ruangan dari controller
        $allRoomsFromDB = $rooms ?? collect();
        
        $allRooms = collect();
        
        // Proses ruangan yang sedang aktif (ada jadwal)
        foreach ($majors as $major) {
            foreach ($major->cctv_rooms as $room) {
                $room->majorId   = $major->id;
                $room->majorName = $major->name;
                $room->majorCode = strtoupper(substr($major->code ?? $major->name, 0, 3));
                $allRooms->push($room);
            }
        }
        
        // Proses ruangan kosong - cek apakah ada relasi ke jurusan via RoomHistory
        foreach ($emptyRooms as $room) {
            // Coba cari jurusan dari room history terakhir
            $lastHistory = $room->roomHistories()
                ->with('classroom.major')
                ->latest('start_date')
                ->first();
            
            if ($lastHistory && $lastHistory->classroom && $lastHistory->classroom->major) {
                // Ruangan ini punya history dengan jurusan tertentu
                $major = $lastHistory->classroom->major;
                $room->majorId   = $major->id;
                $room->majorName = $major->name;
                $room->majorCode = strtoupper(substr($major->code ?? $major->name, 0, 3));
            } else {
                // Ruangan tidak punya history atau tidak terkait jurusan
                $room->majorId   = 'kosong';
                $room->majorName = 'Tidak Aktif';
                $room->majorCode = '';
            }
            
            $allRooms->push($room);
        }
        
        // Ruang Guru (RGURU01) ditaruh paling pertama untuk demo webcam
        $ruangGuruIndex = $allRooms->search(fn($r) => $r->code === 'RGURU01');
        if ($ruangGuruIndex !== false) {
            $ruangGuru = $allRooms->pull($ruangGuruIndex);
            $allRooms->prepend($ruangGuru);
        }

        // Data jurusan untuk dropdown (semua jurusan, bukan hanya aktif)
        $allMajors = $majors;
    @endphp

    <div
        x-data="{
            filterJurusan: 'semua',
            filterLabel: 'Semua Jurusan',
            gridCols: 3,
            settingsOpen: false,
            settingsRooms: [],
            settingsSearch: '',
            settingsLoading: false,
            settingsSaving: false,
            settingsMsg: '',
            settingsMsgType: 'success',
            modalOpen: false,
            modalData: {},
            jam: '',
            dropdownOpen: false,
            searchJurusan: '',
            loadRooms() {
                this.settingsLoading = true;
                fetch('{{ route("admin.cctv.rooms") }}')
                    .then(r => r.json())
                    .then(data => { this.settingsRooms = data; this.settingsLoading = false; })
                    .catch(() => { this.settingsLoading = false; });
            },
            saveRoom(room) {
                this.settingsSaving = true;
                this.settingsMsg = '';
                const body = new FormData();
                body.append('room_id', room.id);
                body.append('camera_type', room.camera_type);
                body.append('is_camera_active', room.is_camera_active ? '1' : '0');
                if (room.stream_url) body.append('stream_url', room.stream_url);
                body.append('_token', document.querySelector('meta[name=csrf-token]').content);
                fetch('{{ route("admin.cctv.settings") }}', { method: 'POST', body })
                    .then(r => r.json())
                    .then(data => {
                        this.settingsSaving = false;
                        this.settingsMsg = data.message;
                        this.settingsMsgType = data.success ? 'success' : 'error';
                        setTimeout(() => { 
                            this.settingsMsg = ''; 
                            // Reload halaman setelah 1 detik supaya perubahan terlihat
                            if (data.success) {
                                setTimeout(() => window.location.reload(), 1000);
                            }
                        }, 2000);
                    })
                    .catch(() => {
                        this.settingsSaving = false;
                        this.settingsMsg = 'Gagal menyimpan, coba lagi.';
                        this.settingsMsgType = 'error';
                    });
            },
            updateJam() {
                const now = new Date();
                this.jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            },
            selectJurusan(id, label) {
                this.filterJurusan = id;
                this.filterLabel = label;
                this.dropdownOpen = false;
                this.searchJurusan = '';
            }
        }"
        x-init="updateJam(); setInterval(() => updateJam(), 1000)"
        class="py-6 space-y-5"
        @click.away="dropdownOpen = false"
    >

        {{-- TOPBAR --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monitor CCTV</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Menampilkan {{ $allRooms->count() }} ruangan</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <x-heroicon-o-clock class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                    <span class="text-sm font-mono text-gray-700 dark:text-gray-300" x-text="jam"></span>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-red-600 dark:text-red-400 tracking-wider">LIVE</span>
                </div>
                {{-- Tombol Pengaturan Kamera --}}
                <button @click="settingsOpen = true; loadRooms()"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-xs font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Pengaturan Kamera
                </button>
                <div class="flex rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button @click="gridCols = 3"
                        :class="gridCols === 3 ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="p-2 transition-colors" title="3 Kolom">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="5" height="5" rx="1"/><rect x="10" y="3" width="5" height="5" rx="1"/><rect x="17" y="3" width="5" height="5" rx="1"/>
                            <rect x="3" y="10" width="5" height="5" rx="1"/><rect x="10" y="10" width="5" height="5" rx="1"/><rect x="17" y="10" width="5" height="5" rx="1"/>
                        </svg>
                    </button>
                    <button @click="gridCols = 2"
                        :class="gridCols === 2 ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="p-2 transition-colors border-l border-gray-200 dark:border-gray-700" title="2 Kolom">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="8" height="8" rx="1"/><rect x="14" y="3" width="8" height="8" rx="1"/>
                            <rect x="3" y="14" width="8" height="8" rx="1"/><rect x="14" y="14" width="8" height="8" rx="1"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-3 flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 text-xs font-medium text-gray-400 dark:text-gray-500">
                <x-heroicon-o-funnel class="w-4 h-4" />
                Filter Jurusan:
            </div>

            {{-- DROPDOWN JURUSAN --}}
            <div class="relative" x-data>
                <button
                    @click="dropdownOpen = !dropdownOpen"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-400 dark:hover:border-gray-500 transition-all min-w-[180px] justify-between"
                >
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-building-office-2 class="w-4 h-4 text-gray-400" />
                        <span class="text-gray-700 dark:text-gray-300" x-text="filterLabel"></span>
                    </div>
                    <svg x-bind:class="dropdownOpen ? 'w-4 h-4 text-gray-400 transition-transform rotate-180' : 'w-4 h-4 text-gray-400 transition-transform'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>

                {{-- DROPDOWN PANEL --}}
                <div
                    x-show="dropdownOpen"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute top-full left-0 mt-1 w-64 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg z-30 overflow-hidden"
                    @click.stop
                >
                    {{-- Search input --}}
                    <div class="p-2 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 flex-shrink-0" />
                            <input
                                type="text"
                                x-model="searchJurusan"
                                placeholder="Cari jurusan..."
                                class="flex-1 bg-transparent text-sm text-gray-700 dark:text-gray-300 outline-none placeholder-gray-400"
                                @click.stop
                            />
                        </div>
                    </div>

                    {{-- Options list --}}
                    <div class="max-h-64 overflow-y-auto py-1">

                        {{-- Semua --}}
                        <button
                            x-show="'semua'.includes(searchJurusan.toLowerCase()) || 'semua jurusan'.includes(searchJurusan.toLowerCase()) || searchJurusan === ''"
                            @click="selectJurusan('semua', 'Semua Jurusan')"
                            :class="filterJurusan === 'semua' ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm transition-colors"
                        >
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-squares-2x2 class="w-4 h-4 text-gray-400" />
                                Semua Jurusan
                            </div>
                            <span class="text-xs font-mono bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-500">
                                {{ $allRooms->count() }}
                            </span>
                        </button>

                        {{-- Per jurusan (tampilkan SEMUA jurusan, tidak peduli aktif atau tidak) --}}
                        @foreach ($allMajors as $major)
                        @php 
                            $isAktif = $major->cctv_rooms->count() > 0;
                            // Hitung total ruangan per jurusan (aktif + kosong)
                            $totalRooms = $allRooms->where('majorId', $major->id)->count();
                        @endphp
                        <button
                            x-show="'{{ strtolower($major->name) }}'.includes(searchJurusan.toLowerCase()) || '{{ strtolower($major->code ?? '') }}'.includes(searchJurusan.toLowerCase()) || searchJurusan === ''"
                            @click="selectJurusan('{{ $major->id }}', '{{ addslashes($major->name) }}')"
                            :class="filterJurusan === '{{ $major->id }}' ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm transition-colors"
                        >
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $isAktif ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                <span>{{ $major->name }}</span>
                                @if ($major->code)
                                    <span class="text-xs text-gray-400 font-mono">({{ $major->code }})</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if ($isAktif)
                                    <span class="text-xs font-mono bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-1.5 py-0.5 rounded">
                                        {{ $major->cctv_rooms->count() }} aktif
                                    </span>
                                @endif
                                <span class="text-xs font-mono bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-500">
                                    {{ $totalRooms }} total
                                </span>
                            </div>
                        </button>
                        @endforeach

                        {{-- Tidak Aktif --}}
                        <button
                            x-show="'tidak aktif'.includes(searchJurusan.toLowerCase()) || 'kosong'.includes(searchJurusan.toLowerCase()) || searchJurusan === ''"
                            @click="selectJurusan('kosong', 'Tidak Aktif')"
                            :class="filterJurusan === 'kosong' ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm transition-colors border-t border-gray-100 dark:border-gray-800 mt-1"
                        >
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                Tidak Aktif / Kosong
                            </div>
                            <span class="text-xs font-mono bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-500">
                                {{ $emptyRooms->count() }}
                            </span>
                        </button>

                    </div>
                </div>
            </div>

            {{-- Indikator filter aktif --}}
            <div x-show="filterJurusan !== 'semua'" class="flex items-center gap-2">
                <span class="text-xs text-gray-500 dark:text-gray-400">Menampilkan:</span>
                <span class="flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    <span x-text="filterLabel"></span>
                </span>
                <button @click="selectJurusan('semua', 'Semua Jurusan')"
                    class="text-xs text-gray-400 hover:text-red-500 transition-colors flex items-center gap-1">
                    <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                    Reset
                </button>
            </div>

            {{-- Stats ringkas --}}
            <div class="ml-auto flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Aktif: <strong class="text-gray-900 dark:text-white ml-0.5">{{ $allRooms->where('majorId', '!=', 'kosong')->count() }}</strong>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    Kosong: <strong class="text-gray-900 dark:text-white ml-0.5">{{ $emptyRooms->count() }}</strong>
                </span>
            </div>
        </div>

        {{-- CAMERA GRID --}}
        <div
            class="grid gap-4"
            :class="{
                'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3': gridCols === 3,
                'grid-cols-1 sm:grid-cols-2': gridCols === 2
            }"
        >
            @foreach ($allRooms as $index => $room)
            @php
                $isAktif     = $room->majorId !== 'kosong';
                
                // CAM ID berdasarkan RUANGAN, bukan kelas
                // Format: CAM-[GEDUNG]-[NOMOR]
                $buildingCode = $room->building ? strtoupper(substr($room->building->name, 0, 3)) : 'GEN';
                $roomNumber = preg_replace('/[^0-9]/', '', $room->code ?? $room->name); // ambil angka dari code/name
                if (empty($roomNumber)) {
                    $roomNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                }
                $camId = 'CAM-' . $buildingCode . '-' . $roomNumber;
                
                $entry       = $isAktif ? $room->activeEntry : null;
                $teacher     = $entry ? optional($entry->teacherSubject)->teacher : null;
                $subject     = $entry ? optional($entry->teacherSubject)->subject : null;
                $classroom   = $entry ? optional($entry->template)->class : null;
                $period      = $entry ? $entry->period : null;
                $jamStr      = $period ? substr($period->start_time, 0, 5) . ' – ' . substr($period->end_time, 0, 5) : '';
                $guruNama    = $teacher?->name ?? '-';
                $guruInisial = $teacher ? strtoupper(substr($teacher->name, 0, 2)) : '?';
                $guruFoto    = ($teacher && $teacher->avatar) ? Storage::url($teacher->avatar) : asset('assets/images/avatar/default-profile.png');
                $kelasNama   = $classroom?->name ?? '-';
                $mapelNama   = $subject?->name ?? '-';
                
                // Nama ruangan yang jelas
                $namaRuangan = $room->name;
                $namaGedung  = optional($room->building)->name ?? 'Gedung Tidak Diketahui';
                
                // Badge jurusan (dari kelas yang sedang pakai, bukan dari ruangan)
                $jurusanBadge = $isAktif && $classroom && $classroom->major 
                    ? strtoupper(substr($classroom->major->code ?? $classroom->major->name, 0, 3))
                    : '';
                
                // Cek apakah ruangan ini pakai webcam dari database
                $isWebcamRoom = ($room->camera_type === 'webcam' && $room->is_camera_active);
            @endphp

            <div
                x-show="filterJurusan === 'semua' || filterJurusan === '{{ $room->majorId }}'"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="group rounded-xl border overflow-hidden cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg
                    {{ $isAktif
                        ? 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-500'
                        : 'bg-gray-50/50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-800 opacity-60 hover:opacity-85' }}"
                @click="
                    modalData = {
                        camId: '{{ $camId }}',
                        ruangan: '{{ addslashes($namaRuangan) }}',
                        gedung: '{{ addslashes($namaGedung) }}',
                        kelas: '{{ addslashes($kelasNama) }}',
                        mapel: '{{ addslashes($mapelNama) }}',
                        jam: '{{ $jamStr }}',
                        guruNama: '{{ addslashes($guruNama) }}',
                        guruInisial: '{{ $guruInisial }}',
                        guruFoto: '{{ $guruFoto }}',
                        isAktif: {{ $isAktif ? 'true' : 'false' }},
                        isWebcam: {{ $isWebcamRoom ? 'true' : 'false' }},
                        roomCode: '{{ $room->code }}',
                        jurusanBadge: '{{ $jurusanBadge }}'
                    };
                    modalOpen = true;
                    setTimeout(function(){
                        var isW = {{ $isWebcamRoom ? 'true' : 'false' }};
                        var modalVideo = document.getElementById('webcam-feed');
                        var ph = document.getElementById('webcam-placeholder');
                        var di = document.getElementById('cam-default-icon');
                        if (isW) {
                            // Ruangan dengan webcam: sambungkan webcam ke modal
                            if (window._webcamStream && modalVideo) {
                                modalVideo.srcObject = window._webcamStream;
                                modalVideo.style.display = 'block';
                                if (ph) ph.style.display = 'none';
                            } else {
                                if (ph) ph.style.display = 'flex';
                                if (modalVideo) modalVideo.style.display = 'none';
                            }
                            if (di) di.style.display = 'none';
                        } else {
                            // Ruangan lain: pastikan webcam TIDAK muncul di modal
                            if (modalVideo) { modalVideo.style.display = 'none'; modalVideo.srcObject = null; }
                            if (ph) ph.style.display = 'none';
                            if (di) di.style.display = 'flex';
                        }
                    }, 50);
                "
            >
                {{-- FEED --}}
                <div class="relative aspect-video bg-gray-950 overflow-hidden">
                    <div class="absolute inset-0 z-10 pointer-events-none"
                        style="background: repeating-linear-gradient(0deg, transparent, transparent 3px, rgba(255,255,255,0.012) 3px, rgba(255,255,255,0.012) 4px)"></div>
                    <div class="absolute inset-0 z-10 pointer-events-none"
                        style="background: radial-gradient(ellipse at center, transparent 55%, rgba(0,0,0,0.55) 100%)"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        @if ($isWebcamRoom)
                            {{-- Webcam live langsung di card --}}
                            <video id="webcam-card-{{ $room->id }}" autoplay playsinline muted
                                class="absolute inset-0 w-full h-full object-cover"
                                style="display:none;z-index:4"></video>
                            {{-- REC badge --}}
                            <div id="rec-badge-{{ $room->id }}" class="absolute top-2 left-2 flex items-center gap-1 px-2 py-0.5 bg-red-600 rounded text-white text-xs font-bold" style="display:none;z-index:20">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse inline-block"></span> REC
                            </div>
                            <div id="webcam-card-placeholder-{{ $room->id }}" class="flex flex-col items-center gap-2" style="z-index:5">
                                <x-heroicon-o-video-camera class="w-10 h-10 text-blue-400 opacity-60" />
                                <span class="text-xs text-blue-400 font-mono opacity-80">WEBCAM READY</span>
                                <button onclick="startCardWebcam('{{ $room->id }}')"
                                    class="mt-1 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                    Aktifkan
                                </button>
                            </div>
                        @elseif ($isAktif)
                            <x-heroicon-o-video-camera class="w-10 h-10 text-gray-700 opacity-20" />
                        @else
                            <div class="flex flex-col items-center gap-1">
                                <x-heroicon-o-video-camera-slash class="w-8 h-8 text-gray-600 opacity-30" />
                                <span class="text-xs text-gray-600 opacity-40 font-mono">NO SIGNAL</span>
                            </div>
                        @endif
                    </div>
                    <div class="absolute top-0 left-0 right-0 z-20 flex items-center justify-between p-2"
                        style="background: linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, transparent 100%)">
                        <span class="text-xs font-mono text-white/60">{{ $camId }}</span>
                        @if ($isAktif)
                            <span class="flex items-center gap-1.5 text-xs font-bold text-red-400 bg-red-500/20 border border-red-500/30 px-2 py-0.5 rounded">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>LIVE
                            </span>
                        @else
                            <span class="text-xs font-bold text-gray-500 bg-gray-500/20 border border-gray-600 px-2 py-0.5 rounded uppercase tracking-wide">Kosong</span>
                        @endif
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 z-20 flex items-end justify-between p-2"
                        style="background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%)">
                        <div class="flex-1 min-w-0">
                            <span class="text-xs font-mono text-white/50 block truncate">{{ $namaGedung }}</span>
                            <span class="text-sm font-semibold text-white block truncate">{{ $namaRuangan }}</span>
                        </div>
                        <span class="text-xs font-mono text-white/40 ml-2" x-text="jam"></span>
                    </div>
                </div>

                {{-- INFO --}}
                <div class="p-3 border-t border-gray-100 dark:border-gray-800">
                    @if ($isAktif)
                        <div class="mb-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Sedang dipakai oleh:</span>
                                @if ($jurusanBadge)
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        {{ $jurusanBadge }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $kelasNama }}</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2.5 flex items-center gap-1 truncate">
                            <x-heroicon-o-book-open class="w-3.5 h-3.5 flex-shrink-0" />
                            {{ $mapelNama }}
                        </p>
                        <div class="flex items-center gap-2.5 p-2 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                            <div class="relative flex-shrink-0">
                                @if ($teacher && $teacher->avatar)
                                    <img src="{{ Storage::url($teacher->avatar) }}" alt="{{ $teacher->name }}"
                                        class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600" />
                                @else
                                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center ring-2 ring-gray-200 dark:ring-gray-600">
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $guruInisial }}</span>
                                    </div>
                                @endif
                                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-white dark:border-gray-800"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $guruNama }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $jamStr }}</p>
                            </div>
                            <x-heroicon-o-signal class="w-3.5 h-3.5 text-green-500 flex-shrink-0" />
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-3 gap-1 text-gray-400 dark:text-gray-600">
                            <x-heroicon-o-minus-circle class="w-5 h-5" />
                            <span class="text-xs">Tidak ada aktivitas</span>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- MODAL --}}
        <div
            x-show="modalOpen"
            x-transition.opacity
            @keydown.escape.window="modalOpen = false; stopWebcam()"
            @click.self="modalOpen = false; stopWebcam()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
            x-cloak
        >
            <div
                x-show="modalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden"
                @click.stop
            >
                <div class="relative aspect-video bg-gray-950">
                    {{-- Tombol close selalu di atas, z-index tinggi --}}
                    <button
                        onclick="stopWebcamGlobal()"
                        @click="modalOpen = false"
                        class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-lg bg-black/60 hover:bg-black/80 text-white transition-colors"
                        style="z-index:100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="absolute inset-0 z-10 pointer-events-none"
                        style="background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.015) 2px, rgba(255,255,255,0.015) 3px)"></div>
                    {{-- Video element --}}
                    <video id="webcam-feed" autoplay playsinline muted
                        class="absolute inset-0 w-full h-full object-cover"
                        style="display:none;z-index:5"></video>

                    {{-- Webcam button overlay (shown/hidden via JS) --}}
                    <div id="webcam-placeholder"
                        class="absolute inset-0 flex flex-col items-center justify-center gap-3"
                        style="display:none;z-index:10">
                        <svg class="w-12 h-12 text-blue-400 opacity-70" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <button id="webcam-btn"
                            class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors"
                            onclick="activateWebcam()">
                            Aktifkan Kamera
                        </button>
                        <p class="text-xs" style="color:rgba(255,255,255,0.4)">Klik untuk akses webcam laptop</p>
                    </div>

                    {{-- Default placeholder --}}
                    <div id="cam-default-icon" class="absolute inset-0 flex items-center justify-center" style="z-index:3">
                        <svg class="w-16 h-16" style="color:rgba(107,114,128,0.15)" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <div class="absolute top-0 left-0 right-0 z-20 flex items-center justify-between p-4"
                        style="background: linear-gradient(to bottom, rgba(0,0,0,0.8) 0%, transparent 100%)">
                        <div class="flex items-center gap-3">
                            <template x-if="modalData.isAktif">
                                <span class="flex items-center gap-1.5 text-xs font-bold text-red-400 bg-red-500/20 border border-red-500/30 px-2.5 py-1 rounded-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>LIVE
                                </span>
                            </template>
                            <span class="text-xs font-mono text-white/50" x-text="modalData.camId"></span>
                        </div>
                        <button @click="modalOpen = false; stopWebcamGlobal()"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 z-20 flex items-end justify-between p-4"
                        style="background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%)">
                        <div>
                            <p class="text-xs text-white/50 mb-0.5" x-text="modalData.gedung"></p>
                            <p class="text-base font-bold text-white" x-text="modalData.ruangan"></p>
                        </div>
                        <span class="text-xs font-mono text-white/40" x-text="jam"></span>
                    </div>
                </div>
                <div class="p-5 flex items-start justify-between gap-4 flex-wrap">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Ruangan:</span>
                            <span class="text-xs font-mono text-gray-400" x-text="modalData.camId"></span>
                        </div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mb-0.5" x-text="modalData.ruangan"></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="modalData.gedung"></p>
                        
                        <template x-if="modalData.isAktif">
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Sedang dipakai oleh:</span>
                                    <span x-show="modalData.jurusanBadge" class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300" x-text="modalData.jurusanBadge"></span>
                                </div>
                                <p class="text-base font-bold text-gray-900 dark:text-white" x-text="modalData.kelas"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1.5">
                                    <x-heroicon-o-book-open class="w-4 h-4 inline flex-shrink-0" />
                                    <span x-text="modalData.mapel"></span>
                                </p>
                                <p class="text-xs text-gray-400 font-mono mt-1" x-text="modalData.jam"></p>
                            </div>
                        </template>
                        <template x-if="!modalData.isAktif">
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-base font-semibold text-gray-500 dark:text-gray-400">Ruangan Tidak Digunakan</p>
                                <p class="text-sm text-gray-400 mt-0.5">Tidak ada kelas yang sedang berlangsung</p>
                            </div>
                        </template>
                    </div>
                    <template x-if="modalData.isAktif">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 min-w-[200px]">
                            <div class="relative flex-shrink-0">
                                <div class="w-11 h-11 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center ring-2 ring-gray-200 dark:ring-gray-600 overflow-hidden">
                                    <img :src="modalData.guruFoto" :alt="modalData.guruNama" class="w-full h-full object-cover"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'" />
                                    <span class="hidden text-sm font-bold text-slate-600 dark:text-slate-300 items-center justify-center w-full h-full absolute inset-0"
                                        x-text="modalData.guruInisial"></span>
                                </div>
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-green-500 border-2 border-white dark:border-gray-800"></span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Pengajar</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="modalData.guruNama"></p>
                                <p class="text-xs text-gray-400 font-mono" x-text="modalData.jam"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ===== MODAL PENGATURAN KAMERA ===== --}}
        <div
            x-show="settingsOpen"
            x-transition.opacity
            @keydown.escape.window="settingsOpen = false"
            @click.self="settingsOpen = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
            style="display: none;"
            x-cloak>
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col" style="max-height: 90vh;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9A2.25 2.25 0 0013.5 5.25h-9A2.25 2.25 0 002.25 7.5v9A2.25 2.25 0 004.5 18.75z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Pengaturan Kamera CCTV</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Atur tipe dan URL kamera per ruangan</p>
                        </div>
                    </div>
                    <button @click="settingsOpen = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Notifikasi --}}
                <div x-show="settingsMsg" x-transition
                    :class="settingsMsgType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
                    class="mx-6 mt-4 px-4 py-2 rounded-lg border text-sm flex items-center gap-2">
                    <span x-text="settingsMsg"></span>
                </div>

                {{-- Search --}}
                <div class="px-6 pt-4 flex-shrink-0">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input x-model="settingsSearch" type="text" placeholder="Cari ruangan..."
                            class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Tabel ruangan --}}
                <div class="flex-1 overflow-y-auto px-6 py-3" style="min-height: 0;">
                    <template x-if="settingsLoading">
                        <div class="flex items-center justify-center py-12 text-gray-400 text-sm gap-2">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Memuat data ruangan...
                        </div>
                    </template>

                    <template x-if="!settingsLoading">
                        <div class="space-y-2">
                            <template x-for="room in settingsRooms.filter(r => r.name.toLowerCase().includes(settingsSearch.toLowerCase()))" :key="room.id">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">

                                    {{-- Info Ruangan --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="room.name"></p>
                                        <p class="text-xs text-gray-400 truncate" x-text="room.building ?? '-'"></p>
                                    </div>

                                    {{-- Toggle aktif/nonaktif --}}
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Aktif</span>
                                        <button
                                            @click="room.is_camera_active = !room.is_camera_active"
                                            :class="room.is_camera_active ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"
                                            class="relative w-9 h-5 rounded-full transition-colors focus:outline-none">
                                            <span :class="room.is_camera_active ? 'translate-x-4' : 'translate-x-0.5'"
                                                class="inline-block w-4 h-4 bg-white rounded-full shadow transition-transform"></span>
                                        </button>
                                    </div>

                                    {{-- Tipe Kamera --}}
                                    <select x-model="room.camera_type"
                                        class="text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="none">Tidak Ada</option>
                                        <option value="webcam">Webcam Laptop</option>
                                        <option value="ip_camera">IP Camera</option>
                                    </select>

                                    {{-- URL Stream --}}
                                    <template x-if="room.camera_type === 'ip_camera'">
                                        <input x-model="room.stream_url" type="url" placeholder="http://192.168.1.x/stream"
                                            class="text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-2 py-1.5 w-48 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </template>

                                    {{-- Tombol Simpan --}}
                                    <button @click="saveRoom(room)"
                                        class="flex-shrink-0 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                        Simpan
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between flex-shrink-0">
                    <p class="text-xs text-gray-400">
                        <span x-text="settingsRooms.filter(r => r.camera_type !== 'none').length"></span> ruangan sudah dikonfigurasi kamera
                    </p>
                    <button @click="settingsOpen = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

<script>
// Global storage untuk multiple webcam streams
window._webcamStreams = {};
window._recorders = {};

// Mulai webcam di card dengan ID spesifik
function startRecording(stream, roomId) {
    var chunks = [];
    var mimeType = MediaRecorder.isTypeSupported('video/webm;codecs=vp9')
        ? 'video/webm;codecs=vp9'
        : MediaRecorder.isTypeSupported('video/webm') ? 'video/webm' : 'video/mp4';
    var recorder = new MediaRecorder(stream, { mimeType: mimeType });
    recorder.ondataavailable = function(e) { if (e.data.size > 0) chunks.push(e.data); };
    recorder.onstop = function() {
        var blob = new Blob(chunks, { type: mimeType });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        var now = new Date();
        var tgl = now.getFullYear() + '-' +
            String(now.getMonth()+1).padStart(2,'0') + '-' +
            String(now.getDate()).padStart(2,'0') + '_' +
            String(now.getHours()).padStart(2,'0') + '-' +
            String(now.getMinutes()).padStart(2,'0') + '-' +
            String(now.getSeconds()).padStart(2,'0');
        a.href = url;
        a.download = 'CCTV_Room_' + roomId + '_' + tgl + '.' + (mimeType.includes('mp4') ? 'mp4' : 'webm');
        a.click();
        URL.revokeObjectURL(url);
        // Update UI rekam
        var recBadge = document.getElementById('rec-badge-' + roomId);
        if (recBadge) recBadge.style.display = 'none';
        chunks = [];
    };
    recorder.start(1000);
    window._recorders[roomId] = recorder;
    // Tampilkan badge REC
    var recBadge = document.getElementById('rec-badge-' + roomId);
    if (recBadge) recBadge.style.display = 'flex';
}

function startCardWebcam(roomId) {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Browser tidak mendukung akses kamera.');
        return;
    }
    
    // Kalau sudah ada stream untuk room ini, jangan buat baru
    if (window._webcamStreams[roomId]) {
        return;
    }
    
    navigator.mediaDevices.getUserMedia({ video: true, audio: true })
        .then(function(stream) {
            window._webcamStreams[roomId] = stream;
            var card = document.getElementById('webcam-card-' + roomId);
            if (card) { card.srcObject = stream; card.style.display = 'block'; }
            var ph = document.getElementById('webcam-card-placeholder-' + roomId);
            if (ph) { ph.style.display = 'none'; }
            // Otomatis mulai rekam
            startRecording(stream, roomId);
        })
        .catch(function(err) {
            alert('Kamera tidak bisa diakses.\nError: ' + err.message);
        });
}

function connectWebcamToModal() {
    if (window._webcamStream) {
        var v = document.getElementById('webcam-feed');
        if (v) { v.srcObject = window._webcamStream; v.style.display = 'block'; }
        var ph = document.getElementById('webcam-placeholder');
        if (ph) { ph.style.display = 'none'; }
    }
}

function activateWebcam() {
    if (window._webcamStream) {
        connectWebcamToModal();
        return;
    }
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Browser tidak mendukung akses kamera.');
        return;
    }
    navigator.mediaDevices.getUserMedia({ video: true, audio: true })
        .then(function(stream) {
            window._webcamStream = stream;
            var v = document.getElementById('webcam-feed');
            if (v) { v.srcObject = stream; v.style.display = 'block'; }
            var ph = document.getElementById('webcam-placeholder');
            if (ph) { ph.style.display = 'none'; }
            var card = document.getElementById('webcam-card');
            if (card) { card.srcObject = stream; card.style.display = 'block'; }
            var cardPh = document.getElementById('webcam-card-placeholder');
            if (cardPh) { cardPh.style.display = 'none'; }
            startRecording(stream);
        })
        .catch(function(err) {
            alert('Kamera tidak bisa diakses.\nError: ' + err.message);
        });
}

function stopWebcamGlobal() {
    // Stop semua recorder
    Object.keys(window._recorders).forEach(function(roomId) {
        var recorder = window._recorders[roomId];
        if (recorder && recorder.state !== 'inactive') {
            recorder.stop();
        }
    });
    window._recorders = {};
    
    // Stop semua stream
    Object.keys(window._webcamStreams).forEach(function(roomId) {
        var stream = window._webcamStreams[roomId];
        if (stream) {
            stream.getTracks().forEach(function(t){ t.stop(); });
        }
        // Reset UI untuk room ini
        var card = document.getElementById('webcam-card-' + roomId);
        if (card) { card.style.display = 'none'; card.srcObject = null; }
        var cardPh = document.getElementById('webcam-card-placeholder-' + roomId);
        if (cardPh) { cardPh.style.display = 'flex'; }
        var recBadge = document.getElementById('rec-badge-' + roomId);
        if (recBadge) { recBadge.style.display = 'none'; }
    });
    window._webcamStreams = {};
    
    // Stop modal webcam juga
    if (window._webcamStream) {
        window._webcamStream.getTracks().forEach(function(t){ t.stop(); });
        window._webcamStream = null;
    }
    var v = document.getElementById('webcam-feed');
    if (v) { v.style.display = 'none'; v.srcObject = null; }
    var ph = document.getElementById('webcam-placeholder');
    if (ph) { ph.style.display = 'none'; }
}
</script>



</x-app-layout>