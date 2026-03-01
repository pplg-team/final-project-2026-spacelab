<x-guest-layout :title="$title" :description="$description">
    @vite('resources/css/home-animations.css')
    @vite('resources/js/home-interactions.js')

    <div class="overflow-hidden">
        <!-- HERO -->
        <section class="relative min py-20 lg:py-36 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-950">
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Text Content -->
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-full text-sm">
                            <x-heroicon-o-check-circle class="w-4 h-4 text-green-500" />
                            <span>Digitalisasi Manajemen Sekolah</span>
                        </div>
                        
                        <h1 class="text-4xl lg:text-5xl font-bold text-slate-900 dark:text-white leading-tight">
                            Kelola Sekolah dengan Lebih Efisien
                        </h1>
                        
                        <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                            Platform terpadu untuk mengelola jadwal, ruangan, guru, dan siswa. Otomatis deteksi konflik dan hemat waktu hingga 70%.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-3 pt-4">
                            <a href="/login" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 dark:bg-slate-700 text-white rounded-lg hover:bg-slate-800 dark:hover:bg-slate-600 transition-colors">
                                <span>Masuk Sistem</span>
                                <x-heroicon-o-arrow-right class="w-4 h-4" />
                            </a>
                            <a href="{{ route('views.views') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors">
                                <span>Jelajahi Fitur</span>
                            </a>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-6 pt-6 border-t border-slate-200 dark:border-slate-800">
                            <div>
                                <div class="text-2xl font-bold text-slate-900 dark:text-white">70%</div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">Hemat Waktu</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-slate-900 dark:text-white">100%</div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">Akurat</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-slate-900 dark:text-white">24/7</div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">Akses</div>
                            </div>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="relative">
                        <img src="{{ asset('assets/images/pages/neskar-ats.webp') }}" 
                             alt="Dashboard SpaceLab"
                             class="w-full rounded-lg shadow-xl border border-slate-200 dark:border-slate-800">
                    </div>
                </div>
            </div>
        </section>

        <!-- PROBLEM STATEMENT -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-slate-900">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                        Tantangan Manajemen Sekolah
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                        Masalah yang sering dihadapi sekolah dalam pengelolaan jadwal dan sumber daya
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div class="p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center mb-4">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                        <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Konflik Jadwal</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Guru mengajar di dua kelas sekaligus, ruangan terpakai ganda karena penjadwalan manual.
                        </p>
                    </div>

                    <div class="p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center mb-4">
                            <x-heroicon-o-clock class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                        </div>
                        <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Proses Lama</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Penyusunan jadwal memakan waktu berhari-hari dengan revisi berkali-kali.
                        </p>
                    </div>

                    <div class="p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center mb-4">
                            <x-heroicon-o-document-text class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Data Tidak Terintegrasi</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Data tersebar di berbagai file Excel, sulit dilacak dan tidak ada sinkronisasi.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SOLUTION -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-950">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                        Solusi SpaceLab
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                        Sistem yang mengotomasi dan menyederhanakan manajemen akademik
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="flex gap-4 p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-600 text-white rounded-lg flex items-center justify-center text-xl font-bold">1</div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Deteksi Konflik Otomatis</h3>
                            <p class="text-slate-600 dark:text-slate-400">
                                Sistem otomatis mendeteksi dan mencegah bentrokan jadwal sebelum disimpan.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-600 text-white rounded-lg flex items-center justify-center text-xl font-bold">2</div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Penjadwalan Cepat</h3>
                            <p class="text-slate-600 dark:text-slate-400">
                                Buat dan edit jadwal dalam hitungan menit dengan antarmuka yang intuitif.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-600 text-white rounded-lg flex items-center justify-center text-xl font-bold">3</div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Platform Terintegrasi</h3>
                            <p class="text-slate-600 dark:text-slate-400">
                                Semua data tersimpan dalam satu sistem yang aman dan mudah diakses.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-600 text-white rounded-lg flex items-center justify-center text-xl font-bold">4</div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Monitoring Real-time</h3>
                            <p class="text-slate-600 dark:text-slate-400">
                                Pantau penggunaan ruangan dan aktivitas sekolah melalui dashboard informatif.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-slate-900">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                        Fitur Lengkap
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                        Semua yang Anda butuhkan untuk mengelola sekolah dengan efisien
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mb-4">
                            <x-heroicon-o-calendar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Manajemen Jadwal</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Buat dan kelola jadwal dengan antarmuka intuitif dan deteksi konflik otomatis.
                        </p>
                    </div>

                    <div class="p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center mb-4">
                            <x-heroicon-o-building-office class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Monitoring Ruangan</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Pantau ketersediaan ruangan secara real-time dan optimalkan penggunaan.
                        </p>
                    </div>

                    <div class="p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center mb-4">
                            <x-heroicon-o-users class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Data Guru & Siswa</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Kelola data lengkap dalam satu sistem dengan kontrol akses berbasis peran.
                        </p>
                    </div>

                    <div class="p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center mb-4">
                            <x-heroicon-o-chart-bar class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                        </div>
                        <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Laporan & Analitik</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Akses laporan komprehensif dengan visualisasi data yang mudah dipahami.
                        </p>
                    </div>

                    <div class="p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center mb-4">
                            <x-heroicon-o-bell class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                        <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Notifikasi Otomatis</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Terima pemberitahuan langsung untuk perubahan jadwal dan informasi penting.
                        </p>
                    </div>

                    <div class="p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center mb-4">
                            <x-heroicon-o-lock-closed class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Keamanan Data</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Data terlindungi dengan enkripsi modern dan backup otomatis.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- USE CASES -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-950">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                        Untuk Semua Peran
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                        Setiap pengguna mendapatkan akses sesuai kebutuhan mereka
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="flex gap-4 p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-user-circle class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Kepala Sekolah</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-3">
                                Dashboard komprehensif untuk monitoring operasional dan laporan statistik.
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 px-2 py-1 rounded">Dashboard</span>
                                <span class="text-xs bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 px-2 py-1 rounded">Laporan</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-academic-cap class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Wakil Kepala / Kurikulum</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-3">
                                Kelola jadwal dengan mudah dan pastikan tidak ada konflik.
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 px-2 py-1 rounded">Penjadwalan</span>
                                <span class="text-xs bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 px-2 py-1 rounded">Alokasi</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Staff Tata Usaha</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-3">
                                Kelola data guru, siswa, dan kelas dengan efisien.
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 px-2 py-1 rounded">Data Management</span>
                                <span class="text-xs bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 px-2 py-1 rounded">Import/Export</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-book-open class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-slate-900 dark:text-white">Guru</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-3">
                                Lihat jadwal mengajar dan akses informasi real-time dari perangkat apapun.
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 px-2 py-1 rounded">Jadwal</span>
                                <span class="text-xs bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 px-2 py-1 rounded">Mobile</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- HOW IT WORKS -->
        <section id="how-it-works" class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-slate-900">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                        Cara Kerja
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                        Tiga langkah sederhana untuk memulai
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 text-white rounded-lg flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                            1
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-slate-900 dark:text-white">Input Data</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Masukkan data guru, siswa, dan ruangan. Import massal dari Excel untuk setup cepat.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-600 text-white rounded-lg flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                            2
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-slate-900 dark:text-white">Susun Jadwal</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Buat jadwal dengan antarmuka intuitif. Sistem otomatis deteksi konflik.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-600 text-white rounded-lg flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                            3
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-slate-900 dark:text-white">Monitor</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Pantau aktivitas real-time dan akses laporan kapan saja.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- BENEFITS -->
        <section id="benefits" class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-950">
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-8">
                            Manfaat untuk Sekolah Anda
                        </h2>
                        <div class="space-y-4">
                            <div class="flex gap-3">
                                <div class="w-6 h-6 bg-green-100 dark:bg-green-900/20 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <x-heroicon-o-check class="w-4 h-4 text-green-600 dark:text-green-400" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Hemat Waktu 70%</h3>
                                    <p class="text-slate-600 dark:text-slate-400">
                                        Penyusunan jadwal yang biasanya berminggu-minggu kini hanya hitungan jam.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <div class="w-6 h-6 bg-green-100 dark:bg-green-900/20 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <x-heroicon-o-check class="w-4 h-4 text-green-600 dark:text-green-400" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Eliminasi Konflik</h3>
                                    <p class="text-slate-600 dark:text-slate-400">
                                        Sistem deteksi otomatis memastikan tidak ada jadwal yang bentrok.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <div class="w-6 h-6 bg-green-100 dark:bg-green-900/20 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <x-heroicon-o-check class="w-4 h-4 text-green-600 dark:text-green-400" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Transparansi Penuh</h3>
                                    <p class="text-slate-600 dark:text-slate-400">
                                        Semua pihak dapat mengakses informasi relevan sesuai peran mereka.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <div class="w-6 h-6 bg-green-100 dark:bg-green-900/20 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <x-heroicon-o-check class="w-4 h-4 text-green-600 dark:text-green-400" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Akses 24/7</h3>
                                    <p class="text-slate-600 dark:text-slate-400">
                                        Platform responsif dapat diakses dari desktop, tablet, atau smartphone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900 rounded-lg p-8 border border-slate-200 dark:border-slate-800">
                        <div class="space-y-6">
                            <div class="border-l-4 border-blue-600 pl-4">
                                <p class="text-slate-700 dark:text-slate-300 italic mb-3">
                                    "SpaceLab sangat membantu kami mengelola 45 kelas dan 80 guru. Penyusunan jadwal yang dulu 2 minggu, sekarang hanya 2 hari."
                                </p>
                                <p class="font-semibold text-slate-900 dark:text-white text-sm">Drs. Ahmad Wijaya, M.Pd</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Kepala Sekolah SMA Negeri 5</p>
                            </div>

                            <div class="border-l-4 border-purple-600 pl-4">
                                <p class="text-slate-700 dark:text-slate-300 italic mb-3">
                                    "Efisiensi penggunaan fasilitas meningkat signifikan. Ruang laboratorium yang dulu sering kosong kini terpakai optimal."
                                </p>
                                <p class="font-semibold text-slate-900 dark:text-white text-sm">Dr. Siti Nurhaliza, S.Pd, M.M</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Wakil Kepala Sekolah SMK Telkom</p>
                            </div>

                            <div class="border-l-4 border-green-600 pl-4">
                                <p class="text-slate-700 dark:text-slate-300 italic mb-3">
                                    "Interface sederhana membuat staff kami yang tidak paham teknologi bisa langsung produktif."
                                </p>
                                <p class="font-semibold text-slate-900 dark:text-white text-sm">Budi Santoso, S.Kom</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Koordinator TI SMP Muhammadiyah</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TRUST -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-slate-900">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                        Sistem Terpercaya
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                        Keamanan dan reliabilitas adalah prioritas kami
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <x-heroicon-o-shield-check class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <h3 class="font-semibold mb-2 text-slate-900 dark:text-white">Keamanan Terjamin</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Data terenkripsi dan backup otomatis untuk melindungi informasi sekolah.
                        </p>
                    </div>

                    <div class="text-center p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <x-heroicon-o-arrow-path class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <h3 class="font-semibold mb-2 text-slate-900 dark:text-white">Uptime 99.9%</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Infrastruktur handal memastikan sistem selalu dapat diakses.
                        </p>
                    </div>

                    <div class="text-center p-6 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <h3 class="font-semibold mb-2 text-slate-900 dark:text-white">Dukungan Responsif</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Tim support siap membantu dengan pelatihan dan troubleshooting.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faqs" class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-950">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                        Pertanyaan Umum
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400">
                        Temukan jawaban atas pertanyaan yang sering diajukan
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white mb-2">Apakah SpaceLab cocok untuk semua jenis sekolah?</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Ya, SpaceLab dirancang untuk semua jenjang pendidikan mulai dari SD, SMP, SMA, hingga SMK. Sistem dapat disesuaikan dengan kebutuhan spesifik setiap institusi.
                        </p>
                    </div>

                    <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white mb-2">Bagaimana sistem mendeteksi konflik jadwal?</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Sistem secara otomatis memeriksa ketersediaan guru, ruangan, dan kelas saat jadwal dibuat. Jika terdeteksi konflik, sistem akan memberikan peringatan.
                        </p>
                    </div>

                    <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white mb-2">Berapa lama waktu implementasi?</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Implementasi awal biasanya 1-2 minggu, termasuk setup sistem, migrasi data, dan pelatihan pengguna.
                        </p>
                    </div>

                    <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white mb-2">Apakah data sekolah aman?</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Sangat aman. Kami menggunakan enkripsi data, autentikasi berlapis, dan backup otomatis harian sesuai standar industri.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900 dark:bg-slate-950 text-white">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                    Siap Transformasi Manajemen Sekolah?
                </h2>
                <p class="text-lg text-slate-300 mb-8 max-w-2xl mx-auto">
                    Bergabunglah dengan ratusan sekolah yang telah merasakan manfaat efisiensi dengan SpaceLab
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/login" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-white text-slate-900 rounded-lg hover:bg-slate-100 transition-colors font-semibold">
                        <span>Masuk ke Sistem</span>
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center gap-2 px-8 py-3 border-2 border-white text-white rounded-lg hover:bg-white hover:text-slate-900 transition-colors font-semibold">
                        <span>Pelajari Fitur</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
</x-guest-layout>
