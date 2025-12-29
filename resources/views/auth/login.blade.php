@extends('layouts.guest')

@section('title', 'Login')

@section('content')
  <div class="flex w-full max-w-5xl shadow-lg rounded-2xl overflow-hidden bg-white mx-auto my-10">
    
    <!-- Bagian Kiri (gambar + overlay) -->
    <div class="hidden md:flex w-1/2 relative bg-gray-200">
      <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f" 
           alt="Background" 
           class="absolute inset-0 w-full h-full object-cover">
      <div class="absolute inset-0 bg-blue-900 opacity-20"></div>
      <div class="relative flex items-start p-6">
        <a href="{{ route('welcome') }}" class="text-white font-semibold flex items-center gap-2 hover:underline">
          <!-- ikon panah -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          KEMBALI
        </a>
      </div>
    </div>

    <!-- Bagian Kanan (form login) -->
    <div class="w-full md:w-1/2 p-12 flex flex-col justify-center">
      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <img src="img/logotsm.png" alt="Logo" class="h-10">
      </div>

      <!-- Judul -->
      <p class="text-center text-lg font-bold text-gray-600 mb-6">
        Selamat datang di Aplikasi Toko Koperasi Tunas Sejahtera Mandiri
      </p>

      <!-- Error Message -->
      @if ($errors->has('login'))
        <div class="p-2 bg-red-100 border border-red-400 text-red-700 rounded">
          {{ $errors->first('login') }}
        </div>
      @endif

      <!-- Form -->
      <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
      @csrf

        <div>
          <label class="block text-gray-700 mb-1">Username</label>
          <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username"
                class="w-full border rounded-md px-3 py-2 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
          @error('username')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-gray-700 mb-1">Password</label>
          <div class="relative">
            <input type="password" id="password" name="password" placeholder="Password"
                   class="w-full border rounded-md px-3 py-2 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <!-- tombol mata -->
            <button type="button" id="togglePassword" 
                    class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
              <!-- ikon default (mata tertutup) -->
              <img id="eyeClosed" src="{{ asset('img/eyeclosed.png') }}" alt="Hide Password" class="h-5 w-5 block">
              <!-- ikon mata terbuka -->
              <img id="eyeOpen" src="{{ asset('img/eyeopen.png') }}" alt="Show Password" class="h-5 w-5 hidden">
            </button>
          </div>
          @error('password')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit" 
                class="w-full bg-blue-700 text-white py-2 rounded-md font-semibold hover:bg-blue-800">
          Log in
        </button>
      </form>

    </div>
  </div>

  <!-- Script toggle password -->
  <script>
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");
    const eyeOpen = document.querySelector("#eyeOpen");
    const eyeClosed = document.querySelector("#eyeClosed");

    // cek elemen sebelum menambahkan event listener
    if (togglePassword && password) {
      togglePassword.addEventListener("click", () => {
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);

        // ganti ikon (jika ada)
        if (eyeOpen && eyeClosed) {
          eyeOpen.classList.toggle("hidden");
          eyeClosed.classList.toggle("hidden");
        }
      });
    }
  </script>
@endsection
