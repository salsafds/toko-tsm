<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Koperasi Tunas Sejahtera Mandiri')</title>

  <link rel="stylesheet" href="{{ asset('css/app.css')}}">
</head>
<body class="bg-gray-50">

  {{-- Header --}}
  <main class="ml-72 p-2">
    @include('layouts.partials.header')
  </main>

  {{-- Sidebar partial --}}
  @include('layouts.partials.sidebarmaster')

  {{-- Konten halaman --}}
  <main class="ml-72 p-6">
    @yield('content')
  </main>

</body>
</html>
