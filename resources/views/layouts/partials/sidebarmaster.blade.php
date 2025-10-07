{{-- resources/views/layouts/sidebarmaster.blade.php --}}
<aside class="fixed left-0 top-0 h-full w-72 bg-white border-r shadow-sm z-40 flex flex-col"
       aria-label="Sidebar Master">

  <!-- Sidebar Header (username + logo tanpa dropdown unit) -->
  <div class="flex items-center gap-3 px-8 pt-4 rounded">
    <div class="flex-1 text-left min-w-0">
      <div class="text-xs text-gray-500">
        Selamat datang, 
      </div>
      <div class="text-lg font-semibold text-gray-900 truncate">
        {{ Auth::check() ? Auth::user()->nama_lengkap : 'Guest' }}
      </div>
    </div>
  </div>

   <hr class="mx-4 my-2 border-gray-200">

    <!-- Quick links under header -->
    <div class="p-4 space-y-2">
      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-50">
        <img src="{{ asset('img/icon/iconHome.png') }}" alt="Icon Home" class="h-5 w-5 object-contain">
        <span class="text-sm text-gray-700">Dashboard</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-50">
        <img src="{{ asset('img/icon/iconLaporan.png') }}" alt="Icon Laporan" class="h-5 w-5 object-contain">
        <span class="text-sm text-gray-700">Laporan</span>
      </a>
    </div>
  </div>

  <hr class="mx-4 my-2 border-gray-200">

  <!-- Sidebar Body -->
  <div class="flex-1 overflow-y-auto">
    <nav class="p-4 space-y-2" aria-label="Main navigation">
      <div class="text-xs font-semibold text-gray-500 uppercase px-2">Main</div>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <img src="{{ asset('img/icon/iconBarang.png') }}" alt="Icon Barang" class="h-5 w-5 object-contain">
        <span class="text-sm text-gray-700">CRUD Data Barang</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <img src="{{ asset('img/icon/iconSupplier.png') }}" alt="Icon Supplier" class="h-5 w-5 object-contain">
        <span class="text-sm text-gray-700">CRUD Data Supplier</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <img src="{{ asset('img/icon/iconPelanggan.png') }}" alt="Icon Pelanggan" class="h-5 w-5 object-contain">
        <span class="text-sm text-gray-700">CRUD Data Pelanggan</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <img src="{{ asset('img/icon/iconKaryawan.png') }}" alt="Icon Karyawan" class="h-5 w-5 object-contain">
        <span class="text-sm text-gray-700">CRUD Data Karyawan</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 7l2 13h12l2-13"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 11-8 0"/>
        </svg>
        <span class="text-sm text-gray-700">Orders</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 8a9 9 0 0118 0"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l10-8"/>
        </svg>
        <span class="text-sm text-gray-700">Broadcasts</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09c.34.06.66.22.93.46"/>
        </svg>
        <span class="text-sm text-gray-700">Settings</span>
      </a>

      <div class="h-6"></div>

      <div class="text-xs font-semibold text-gray-500 uppercase px-2">Data Konfigurasi</div>

      <a href="{{ route('master.dataSatuan.index') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 110 20 10 10 0 010-20z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 14s1.5 2 4 2 4-2 4-2"/>
        </svg>
        <span class="text-sm text-gray-700">Data Satuan</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-sm text-gray-700">Changelog</span>
      </a>
    </nav>
  </div>

  <!-- Sidebar Footer (profile dropdown dari database) -->
  <div class="border-t p-4">
    @php
      $user = Auth::user();
      $foto = $user && $user->foto_user
          ? asset('storage/' . $user->foto_user)
          : asset('img/iconProfil.jpg');
    @endphp

    <div x-data="{ open: false }" class="relative">
      <button @click="open = !open" class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-50 focus:outline-none">
        <img src="{{ $foto }}" alt="Profile" class="h-10 w-10 rounded object-cover">
        <div class="min-w-0">
          <div class="truncate text-sm font-medium text-gray-900">
            {{ $user ? $user->nama_lengkap : 'Guest' }}
          </div>
          <div class="truncate text-xs text-gray-500">
            {{ $user ? ucfirst($user->role) : 'Role Tidak Dikenal' }}
          </div>
        </div>
        <svg x-show="!open" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 8l4 4 4-4"/>
        </svg>
        <svg x-show="open" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12l4-4 4 4"/>
        </svg>
      </button>

      <div x-show="open" x-transition @click.outside="open = false" class="absolute left-0 bottom-12 w-full bg-white border rounded shadow-md overflow-hidden z-10">
        <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50">
          <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11c1.657 0 3-1.343 3-3S17.657 5 16 5s-3 1.343-3 3 1.343 3 3 3zM8 11c1.657 0 3-1.343 3-3S9.657 5 8 5 5 6.343 5 8s1.343 3 3 3z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 20c0-3.314 4.03-6 9-6s9 2.686 9 6"/>
          </svg>
          <span class="text-sm text-gray-700">My profile</span>
        </a>

        <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50">
          <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09c.34.06.66.22.93.46"/>
          </svg>
          <span class="text-sm text-gray-700">Settings</span>
        </a>

        <div class="border-t"></div>

        <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50">
          <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m0 12v3m9-9h-3M6 12H3"/>
          </svg>
          <span class="text-sm text-gray-700">Privacy policy</span>
        </a>

        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 hover:bg-gray-50 text-left">
            <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5"/>
            </svg>
            <span class="text-sm text-gray-700">Sign out</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</aside>
