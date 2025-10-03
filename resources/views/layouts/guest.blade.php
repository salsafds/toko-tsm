<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Koperasi Tunas Sejahtera Mandiri')</title>

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100">

  {{-- Konten halaman --}}
  <main class="flex-1">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="mt-2">
    <div class="container mx-auto px-4 py-6 text-sm text-gray-600 text-center">
      &copy; {{ date('Y') }} Koperasi Tunas Sejahtera Mandiri. All rights reserved.
    </div>
  </footer>

</body>
</html>
