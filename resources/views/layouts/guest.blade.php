<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Koperasi Tunas Sejahtera Mandiri')</title>

  <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-gray-100">

  {{-- Konten halaman --}}
  <main class="flex-1">
    @yield('content')
  </main>

  {{-- Footer --}}
  @include('layouts.partials.footer')

</body>
</html>
