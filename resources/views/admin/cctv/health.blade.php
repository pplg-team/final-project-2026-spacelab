<x-app-layout title="Status Kesehatan Kamera" description="Monitor status dan kesehatan kamera CCTV">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800  leading-tight">
                    Status Kesehatan Kamera
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6" id="cctv-health-page" data-summary-url="{{ route('admin.cctv.health.summary') }}">
        <div class="space-y-6">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <a href="{{ route('admin.cctv.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white  border border-gray-300  md font-semibold text-xs text-gray-700  uppercase tracking-widest shadow-sm hover:bg-gray-50  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2  transition ease-in-out duration-150">
                    <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                    Kembali
                </a>
            </div>
            <div class="bg-white  shadow-sm sm:lg border border-gray-200  overflow-hidden">
                <div class="p-6 border-b border-gray-200 ">
                    <h3 class="text-lg font-medium text-gray-900 ">Ringkasan Status</h3>
                    <p class="mt-1 text-sm text-gray-600 ">Status kesehatan kamera secara keseluruhan</p>
                </div>

                <div class="p-4 bg-gray-50/70 ">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3" id="health-summary">
                        <div class="xl border border-blue-200  bg-blue-50  p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-blue-700 ">Total Kamera</p>
                            <p class="mt-2 text-2xl font-bold text-blue-900 " id="stat-total">-</p>
                        </div>
                        <div class="xl border border-green-200  bg-green-50  p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-green-700 ">Online</p>
                            <p class="mt-2 text-2xl font-bold text-green-900 " id="stat-online">-</p>
                        </div>
                        <div class="xl border border-yellow-200  bg-yellow-50  p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-yellow-700 ">Degraded</p>
                            <p class="mt-2 text-2xl font-bold text-yellow-900 " id="stat-degraded">-</p>
                        </div>
                        <div class="xl border border-red-200  bg-red-50  p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-red-700 ">Offline</p>
                            <p class="mt-2 text-2xl font-bold text-red-900 " id="stat-offline">-</p>
                        </div>
                        <div class="xl border border-gray-200  bg-gray-50  p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-700 ">Unknown</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900 " id="stat-unknown">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white  shadow-sm sm:lg border border-gray-200  overflow-hidden">
                <div class="p-6 border-b border-gray-200 ">
                    <h3 class="text-lg font-medium text-gray-900 ">Daftar Kamera</h3>
                    <p class="mt-1 text-sm text-gray-600 ">Status detail setiap kamera</p>
                </div>

                <div class="p-4 border-b border-gray-200  bg-gray-50/70 ">
                    <form method="GET" action="{{ route('admin.cctv.health.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <select name="building_id"
                            class="md border-gray-300    shadow-sm focus:border-gray-500  focus:ring-gray-500  text-sm">
                            <option value="all" @selected($buildingId === 'all')>Semua Gedung</option>
                            @foreach ($buildings as $building)
                                <option value="{{ $building->id }}" @selected($buildingId === (string) $building->id)>
                                    {{ $building->code }} - {{ $building->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status"
                            class="md border-gray-300    shadow-sm focus:border-gray-500  focus:ring-gray-500  text-sm">
                            <option value="all" @selected($status === 'all')>Semua Status</option>
                            <option value="online" @selected($status === 'online')>Online</option>
                            <option value="degraded" @selected($status === 'degraded')>Degraded</option>
                            <option value="offline" @selected($status === 'offline')>Offline</option>
                            <option value="unknown" @selected($status === 'unknown')>Unknown</option>
                        </select>

                        <select name="per_page"
                            class="md border-gray-300    shadow-sm focus:border-gray-500  focus:ring-gray-500  text-sm">
                            <option value="10" @selected($perPage === 10)>10 / halaman</option>
                            <option value="15" @selected($perPage === 15)>15 / halaman</option>
                            <option value="25" @selected($perPage === 25)>25 / halaman</option>
                            <option value="50" @selected($perPage === 50)>50 / halaman</option>
                        </select>

                        <div class="flex items-center gap-2">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800  border border-transparent md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700  active:bg-gray-900  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2  transition ease-in-out duration-150">
                                Terapkan Filter
                            </button>

                            @if ($buildingId !== 'all' || $status !== 'all' || $perPage !== 10)
                                <a href="{{ route('admin.cctv.health.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white  border border-gray-300  md font-semibold text-xs text-gray-700  uppercase tracking-widest shadow-sm hover:bg-gray-50  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2  transition ease-in-out duration-150">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto divide-y divide-gray-200 ">
                        <thead class="bg-gray-50 ">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 ">Ruangan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 ">Gedung</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 ">Tipe Kamera</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 ">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 ">Terakhir Cek</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 ">Response Time</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 ">Error</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200  bg-white ">
                            @forelse ($rooms as $room)
                                @php
                                    $healthLog = $room->latestHealthLog;
                                    $statusValue = $healthLog?->status ?? 'unknown';
                                    $statusBadge = match($statusValue) {
                                        'online' => 'bg-green-100 text-green-700  ',
                                        'degraded' => 'bg-yellow-100 text-yellow-700  ',
                                        'offline' => 'bg-red-100 text-red-700  ',
                                        default => 'bg-gray-100 text-gray-700  ',
                                    };
                                    $statusLabel = match($statusValue) {
                                        'online' => 'Online',
                                        'degraded' => 'Degraded',
                                        'offline' => 'Offline',
                                        default => 'Unknown',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 ">
                                    <td class="px-4 py-4">
                                        <p class="text-sm font-semibold text-gray-900 ">{{ $room->name }}</p>
                                        <p class="text-xs text-gray-500 ">{{ $room->code }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 ">
                                        {{ optional($room->building)->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        @php
                                            $cameraTypeValue = $room->camera_type ?? 'none';
                                            $cameraTypeClass = match($cameraTypeValue) {
                                                'ip_camera' => 'bg-indigo-100 text-indigo-700  ',
                                                'webcam' => 'bg-blue-100 text-blue-700  ',
                                                default => 'bg-gray-100 text-gray-700  ',
                                            };
                                            $cameraTypeLabel = match($cameraTypeValue) {
                                                'ip_camera' => 'IP Camera',
                                                'webcam' => 'Webcam',
                                                default => 'Tidak Ada',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold {{ $cameraTypeClass }}">
                                            {{ $cameraTypeLabel }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold {{ $statusBadge }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 ">
                                        {{ $healthLog ? $healthLog->checked_at->diffForHumans() : '-' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 ">
                                        {{ $healthLog && $healthLog->response_ms ? $healthLog->response_ms . ' ms' : '-' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 ">
                                        @if ($healthLog && $healthLog->error_message)
                                            <span class="text-xs text-red-600 " title="{{ $healthLog->error_message }}">
                                                {{ Str::limit($healthLog->error_message, 30) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 ">
                                        Tidak ada data kamera yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($rooms->total() > 0)
                    @php
                        $currentPage = $rooms->currentPage();
                        $lastPage = $rooms->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp

                    <div class="px-4 py-4 border-t border-gray-200  bg-gray-50/70 ">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                            <div class="text-xs text-gray-500  space-y-1">
                                <p>Menampilkan {{ $rooms->firstItem() }} - {{ $rooms->lastItem() }} dari {{ $rooms->total() }} data</p>
                                <p>Halaman {{ $currentPage }} dari {{ $lastPage }}</p>
                            </div>

                            <div class="flex items-center flex-wrap gap-2">
                                <a href="{{ $rooms->url(1) }}"
                                    class="{{ $rooms->onFirstPage() ? 'pointer-events-none opacity-50' : '' }} inline-flex items-center px-3 py-1.5 md border border-gray-300  bg-white  text-xs font-medium text-gray-700  hover:bg-gray-50 ">
                                    Pertama
                                </a>

                                <a href="{{ $rooms->previousPageUrl() ?? '#' }}"
                                    class="{{ $rooms->onFirstPage() ? 'pointer-events-none opacity-50' : '' }} inline-flex items-center px-3 py-1.5 md border border-gray-300  bg-white  text-xs font-medium text-gray-700  hover:bg-gray-50 ">
                                    Sebelumnya
                                </a>

                                @for ($page = $startPage; $page <= $endPage; $page++)
                                    <a href="{{ $rooms->url($page) }}"
                                        class="{{ $page === $currentPage ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white  border-gray-300  text-gray-700  hover:bg-gray-50 ' }} inline-flex items-center justify-center w-9 h-9 md border text-xs font-semibold">
                                        {{ $page }}
                                    </a>
                                @endfor

                                <a href="{{ $rooms->nextPageUrl() ?? '#' }}"
                                    class="{{ $rooms->hasMorePages() ? '' : 'pointer-events-none opacity-50' }} inline-flex items-center px-3 py-1.5 md border border-gray-300  bg-white  text-xs font-medium text-gray-700  hover:bg-gray-50 ">
                                    Berikutnya
                                </a>

                                <a href="{{ $rooms->url($lastPage) }}"
                                    class="{{ $currentPage === $lastPage ? 'pointer-events-none opacity-50' : '' }} inline-flex items-center px-3 py-1.5 md border border-gray-300  bg-white  text-xs font-medium text-gray-700  hover:bg-gray-50 ">
                                    Terakhir
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @vite('resources/js/admin/cctv-health.js')
</x-app-layout>
