<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Koperasi Tunas Sejahtera Mandiri')</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">

  {{-- Header --}}
  <main class="ml-72 p-2">
    @include('layouts.partials.header')
  </main>

  {{-- Sidebar partial --}}
  @include('layouts.partials.sidebarmaster')

  {{-- Konten halaman --}}
  <main class="ml-72 p-6 py-4">
    @yield('content')
  </main>

  {{-- Footer --}}
  <main class="ml-72 p-2">
    @include('layouts.partials.footer')
  </main>
</body>
</html>
