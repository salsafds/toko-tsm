@extends('layouts.app')

@section('title', 'Koperasi Toko')

@section('content')
  <!-- Hero Section -->
  <section class="relative h-screen flex items-center justify-center">
    <!-- Background -->
    <img src="{{ asset('img/landing.png') }}" 
          alt="Background"
          class="absolute inset-0 w-full h-full object-cover rounded-2xl shadow-lg">

    <!-- Overlay -->
    <div class="absolute inset-0 opacity-40 rounded-2xl"></div>

    <!-- Content -->
    <div class="relative text-center text-white max-w-3xl px-6">
      <h1 class="text-2xl font-bold leading-tight">Selamat Datang di</h1>
      <h2 class="text-xl md:text-5xl font-bold block text-blue-800">Koperasi Karyawan</h2>
      <h2 class="text-xl md:text-5xl font-bold block text-blue-800">Tunas Sejahtera Mandiri</h2>
      <p class="mt-4 text-lg text-white font-medium">
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
@endsection
