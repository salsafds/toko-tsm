<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin Master</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="h-full">
  <div class="flex h-screen bg-gray-100">

    <!-- Sidebar -->
    <div class="w-64 bg-indigo-700 text-white flex flex-col">
      <div class="p-4 text-2xl font-bold">TokoKu</div>
      <nav class="flex-1 px-2 space-y-2">
        <a href="#" class="flex items-center px-4 py-2 bg-indigo-900 rounded-lg">
          <span class="material-icons mr-2">dashboard</span> Dashboard
        </a>
        <a href="#" class="flex items-center px-4 py-2 hover:bg-indigo-600 rounded-lg">
          <span class="material-icons mr-2">inventory_2</span> Produk
        </a>
        <a href="#" class="flex items-center px-4 py-2 hover:bg-indigo-600 rounded-lg">
          <span class="material-icons mr-2">shopping_cart</span> Transaksi
        </a>
        <a href="#" class="flex items-center px-4 py-2 hover:bg-indigo-600 rounded-lg">
          <span class="material-icons mr-2">groups</span> Pengguna
        </a>
        <a href="#" class="flex items-center px-4 py-2 hover:bg-indigo-600 rounded-lg">
          <span class="material-icons mr-2">assessment</span> Laporan
        </a>
      </nav>
      <div class="p-4 border-t border-indigo-600">
       <a href="#" class="flex items-center px-4 py-2 hover:bg-indigo-600 rounded-lg">
        <span class="material-icons mr-2">logout</span> Logout
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="bg-white shadow p-4 flex justify-between items-center">
        <div class="text-xl font-semibold">Dashboard Admin Master</div>
        <div class="flex items-center space-x-4">
          <span class="text-gray-700">{{ auth()->user()->name ?? 'Admin Master' }}</span>
          <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin Master') }}" 
               alt="avatar" class="w-10 h-10 rounded-full">
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 p-6">
        <div class="grid grid-cols-4 gap-6">
          <!-- Card contoh -->
          <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Total Produk</h3>
            <p class="text-2xl font-bold mt-2">120</p>
          </div>
          <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Total Transaksi</h3>
            <p class="text-2xl font-bold mt-2">350</p>
          </div>
          <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Total User</h3>
            <p class="text-2xl font-bold mt-2">50</p>
          </div>
          <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Pendapatan</h3>
            <p class="text-2xl font-bold mt-2">Rp 25.000.000</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</body>
</html>
