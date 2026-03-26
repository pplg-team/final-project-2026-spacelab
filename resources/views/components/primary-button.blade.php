{{--
Primary Button Component
=========================
Tombol utama dengan styling yang konsisten.

Penggunaan:
  <x-primary-button>Klik Saya</x-primary-button>
  
Dengan width custom:
  <x-primary-button :width="'w-32'">Tombol Kecil</x-primary-button>
  <x-primary-button :width="'w-auto'">Tombol Auto</x-primary-button>
  
Atribut:
  - width: Class Tailwind untuk lebar (default: 'w-full')
  - Semua atribut HTML standar didukung (disabled, class, dll)
--}}

@php
  $widthClass = $width ?? 'w-full';
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => $widthClass . ' flex items-center justify-center gap-2 px-4 py-2.5 bg-[#2563eb] dark:bg-[#3b82f6] text-white hover:bg-[#1d4ed8] dark:hover:bg-[#2d53c4] focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-700 focus:ring-offset-2 transition font-medium']) }}>
    {{ $slot }}
</button>
