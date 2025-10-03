<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Koperasi Toko')</title>

  <!-- Pakai asset agar sama dengan welcome & login -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100">

  {{-- Header sederhana (bisa di-override jika perlu) --}}
  <header class="bg-transparent">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ route('welcome') }}" class="flex items-center gap-3">
        <div class="h-8 w-8 bg-blue-800 rounded-md"></div>
        <span class="font-semibold text-lg text-blue-800">Koperasi Tunas Sejahtera Mandiri</span>
      </a>

      <nav class="hidden md:flex items-center gap-4">
        <a href="{{ route('welcome') }}" class="text-sm text-gray-700 hover:underline">Beranda</a>
        <a href="{{ route('login') }}" class="text-sm px-3 py-1 border rounded text-gray-700 hover:bg-blue-50">Login</a>
      </nav>

      <!-- Mobile toggle (kosong, hanya placeholder) -->
      <button class="md:hidden" aria-label="menu">
        <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>
  </header>

  {{-- Konten halaman --}}
  <main class="flex-1">
    @yield('content')
  </main>

  {{-- Footer minimal --}}
  <footer class="mt-8">
    <div class="container mx-auto px-4 py-6 text-sm text-gray-600 text-center">
      &copy; {{ date('Y') }} Koperasi Tunas Sejahtera Mandiri. All rights reserved.
    </div>
  </footer>

</body>
</html>
