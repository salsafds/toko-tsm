<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Koperasi Tunas Sejahtera Mandiri')</title>

  <!-- Pakai asset CSS (sama seperti welcome & login) -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100">

  {{-- Header --}}
  <header class="bg-transparent">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">

      <!-- Left: brand name + alamat -->
      <div class="flex items-start gap-3">
        <div class="h-10 w-10 bg-blue-800 rounded-md flex-shrink-0"></div>
        <div>
          <div class="font-semibold text-lg text-blue-800">Koperasi Tunas Sejahtera Mandiri</div>
          <div class="text-sm text-gray-600 mt-1">
            Jl. Karah Agung 45, Surabaya, Jawa Timur, Indonesia.
          </div>
        </div>
      </div>

      <!-- Right: navbar placeholder + mobile toggle + logo -->
      <div class="flex items-center gap-4">
        <!-- Navbar empty (keperluan desktop) -->
        <nav class="hidden md:flex items-center gap-4">
          <!-- intentionally left blank (no Beranda / Login links) -->
        </nav>

        <!-- Mobile toggle -->
        <button class="md:hidden" aria-label="menu" id="mobileBtn">
          <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>

        <!-- Logo koperasi (di sebelah navbar/mobile toggle) -->
        <div class="flex items-center">
          <img src="{{ asset('img/logo.png') }}" alt="Logo Koperasi" class="h-10 w-10 object-contain rounded-md shadow-sm">
        </div>
      </div>
    </div>
  </header>

  {{-- Konten halaman --}}
  <main class="flex-1">
    @yield('content')
  </main>

  {{-- Footer minimal --}}
  <footer class="mt-4">
    <div class="container mx-auto px-4 py-6 text-sm text-gray-600 text-center">
      &copy; {{ date('Y') }} Koperasi Tunas Sejahtera Mandiri. All rights reserved.
    </div>
  </footer>

  <!-- optional small script for mobile toggle (keperluan nanti) -->
  <script>
    document.getElementById('mobileBtn')?.addEventListener('click', function(){
      // placeholder behavior — jika kedepan mau toggle menu mobile, implementasikan di sini
      const nav = document.querySelector('nav');
      if(!nav) return;
      nav.classList.toggle('hidden');
    });
  </script>
</body>
</html>
