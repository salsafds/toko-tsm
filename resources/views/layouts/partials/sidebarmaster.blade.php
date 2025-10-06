{{-- resources/views/layouts/sidebarmaster.blade.php --}}
<aside class="fixed left-0 top-0 h-full w-72 bg-white border-r shadow-sm z-40 flex flex-col"
       aria-label="Sidebar Master">

  <!-- Sidebar Header (workspace / team dropdown) -->
  <div class="px-4 py-4 border-b">
    <div x-data="{ open: false }" class="relative">
      <button @click="open = !open"
              :aria-expanded="open.toString()"
              class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
              aria-haspopup="true">
        <img src="{{ asset('img/logotsm.png') }}" alt="Logo" class="h-8 w-8 rounded object-contain">
        <div class="flex-1 text-left min-w-0">
          <div class="truncate text-sm font-medium text-gray-900">Koperasi TSM</div>
          <div class="truncate text-xs text-gray-500">Pilih Unit Kerja</div>
        </div>
        <!-- chevron -->
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
        </svg>
        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 15l6-6 6 6"/>
        </svg>
      </button>

      <!-- Dropdown menu -->
      <div x-show="open" x-transition @click.outside="open = false"
           class="mt-2 bg-white border rounded shadow-sm overflow-hidden"
           style="min-width:220px;">
        <a href="#" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50">
          <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 17a4 4 0 100-8 4 4 0 000 8z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
          </svg>
          <span class="text-sm text-gray-700">Unit 1</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50">
          <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 17a4 4 0 100-8 4 4 0 000 8z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
          </svg>
          <span class="text-sm text-gray-700">Unit 2</span>
        </a>

        <div class="border-t"></div>

        <a href="#" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50">
          <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16"/>
          </svg>
          <span class="text-sm text-gray-700">Buat Unit Baru</span>
        </a>
      </div>
    </div>

    <!-- Quick links under header -->
    <div class="mt-3 space-y-1">
      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
        </svg>
        <span class="text-sm text-gray-700">Dashboard</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 8v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8"/>
        </svg>
        <span class="text-sm text-gray-700">Laporan</span>
      </a>
    </div>
  </div>

  <!-- Sidebar Body -->
  <div class="flex-1 overflow-y-auto">
    <nav class="p-4 space-y-2" aria-label="Main navigation">
      <div class="text-xs font-semibold text-gray-500 uppercase px-2">Main</div>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9v7a2 2 0 01-2 2h-4a2 2 0 01-2-2V12H9v7a2 2 0 01-2 2H3z"/>
        </svg>
        <span class="text-sm text-gray-700">CRUD Data Barang</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
        </svg>
        <span class="text-sm text-gray-700">Events</span>
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

      <div class="text-xs font-semibold text-gray-500 uppercase px-2">Support</div>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 110 20 10 10 0 010-20z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 14s1.5 2 4 2 4-2 4-2"/>
        </svg>
        <span class="text-sm text-gray-700">Support</span>
      </a>

      <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">
        <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-sm text-gray-700">Changelog</span>
      </a>
    </nav>
  </div>

  <!-- Sidebar Footer (profile dropdown) -->
  <div class="border-t p-4">
    <div x-data="{ open: false }" class="relative">
      <button @click="open = !open" class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-50 focus:outline-none">
        <img src="{{ asset('img/profile-photo.jpg') }}" alt="Profile" class="h-10 w-10 rounded object-cover">
        <div class="min-w-0">
          <div class="truncate text-sm font-medium text-gray-900">Erica</div>
          <div class="truncate text-xs text-gray-500">erica@example.com</div>
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

        <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50">
          <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5"/>
          </svg>
          <span class="text-sm text-gray-700">Sign out</span>
        </a>
      </div>
    </div>
  </div>
</aside>
