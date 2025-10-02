<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>signin</title>
 <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="flex w-full max-w-5xl shadow-lg rounded-2xl overflow-hidden bg-white">
    
    <!-- Bagian Kiri (gambar + overlay) -->
    <div class="hidden md:flex w-1/2 relative bg-gray-200">
      <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f" 
           alt="Background" 
           class="absolute inset-0 w-full h-full object-cover">
      <div class="absolute inset-0 bg-blue-900 opacity-20"></div>
      <div class="relative flex items-start p-6">
        <a href="../" class="text-white font-semibold flex items-center gap-2 hover:underline">
          <!-- ikon panah -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          KEMBALI
        </a>
      </div>
    </div>

    <!-- Bagian Kanan (form login) -->
    <div class="w-full md:w-1/2 p-8 flex flex-col justify-center">
      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <img src="https://dummyimage.com/80x80/000/fff&text=Logo" alt="Logo" class="h-16">
      </div>

      <!-- Judul -->
      <h2 class="text-center text-xl font-bold text-gray-700 mb-2">
        Login
      </h2>
      <p class="text-center text-sm text-gray-600 mb-6">
        Selamat datang di Aplikasi Koperasi Temprina Sejahtera Mandiri
      </p>

      <!-- Form -->
      <form action="#" method="POST" class="space-y-5">
        @csrf

        <div>
          <label class="block text-gray-700 mb-1">Username</label>
          <input type="text" name="username" placeholder="Masukkan username"
                 class="w-full border rounded-md px-3 py-2 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
              <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-1.036.158-2.035.45-2.975m3.15-3.15A9.959 9.959 0 0112 3c5.523 0 10 4.477 10 10 0 1.036-.158 2.035-.45 2.975m-3.15 3.15L4.5 4.5" />
              </svg>
              <!-- ikon mata terbuka -->
              <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" 
                class="w-full bg-blue-700 text-white py-2 rounded-md font-semibold hover:bg-blue-800">
          LOGIN
        </button>
      </form>

      <!-- Forgot password -->
      <p class="text-center text-sm text-gray-600 mt-4">
        Lupa password? <a href="#" class="text-blue-500 hover:underline">Click here</a>
      </p>
    </div>
  </div>

  <!-- Script toggle password -->
  <script>
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");
    const eyeOpen = document.querySelector("#eyeOpen");
    const eyeClosed = document.querySelector("#eyeClosed");

    togglePassword.addEventListener("click", () => {
      const type = password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);

      // ganti ikon
      eyeOpen.classList.toggle("hidden");
      eyeClosed.classList.toggle("hidden");
    });
  </script>

</body>
</html>
