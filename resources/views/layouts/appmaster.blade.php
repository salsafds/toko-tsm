<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Koperasi Tunas Sejahtera Mandiri')</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50" x-data="{ isOpen: true }">>

  {{-- Header --}}
  <main class="fixed top-0 right-0 left-0 transition-all duration-300" :class="{ 'ml-72': isOpen, 'ml-16': !isOpen }">
    @include('layouts.partials.header')
  </main>

  {{-- Sidebar partial --}}
  @include('layouts.partials.sidebarmaster')

  {{-- Konten halaman --}}
  <main class=" pt-16 transition-all duration-300 px-6" :class="{ 'ml-72': isOpen, 'ml-16': !isOpen }">
    @yield('content')
  </main>

  {{-- Footer --}}
  <main class="transition-all duration-300 p-2" :class="{ 'ml-72': isOpen, 'ml-16': !isOpen }">
    @include('layouts.partials.footer')
  </main>
</body>
</html>
