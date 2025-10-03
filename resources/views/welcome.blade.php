<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Koperasi Toko</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100">

  <!-- Hero Section -->
  <section class="relative h-screen flex items-center justify-center">
    <!-- Background -->
    <img src="https://images.unsplash.com/photo-1580910051074-cd1e7b3d0e09?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80" 
          alt="Background"
          class="absolute inset-0 w-full h-full object-cover rounded-2xl shadow-lg">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black bg-opacity-40 rounded-2xl"></div>

    <!-- Content -->
    <div class="relative text-center text-white max-w-3xl px-6">
      <h1 class="text-xl md:text-5xl font-bold leading-tight">
        Selamat Datang di
        <span class="block text-blue-800">Koperasi Tunas Sejahtera Mandiri </span>
      </h1>
      <p class="mt-4 text-lg text-gray-200">
        Koperasi kami hadir untuk mendukung kebutuhan anggota, meningkatkan kesejahteraan, 
        dan menciptakan kemandirian ekonomi berkelanjutan.
      </p>
      <div class="mt-6">
        <a href="{{ route('login') }}" 
           class="px-6 py-3 bg-blue-800 text-white font-semibold rounded-full hover:bg-blue-600 transition">
          Login ke Aplikasi
        </a>
      </div>
    </div>
  </section>

</body>
</html>
