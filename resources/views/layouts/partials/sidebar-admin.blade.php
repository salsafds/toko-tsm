<style>
  [x-cloak] {
    display: none !important;
  }
  aside {
    width: 18rem; /* Fallback untuk w-72 */
  }
  aside.w-16 {
    width: 4rem; /* Fallback untuk w-16 */
  }
  aside:not(.w-72) .overflow-y-auto::-webkit-scrollbar {
    display: none;
  }
  aside:not(.w-72) .overflow-y-auto {
    -ms-overflow-style: none; /* Untuk Internet Explorer dan Edge */
    scrollbar-width: none; /* Untuk Firefox */
  }
  @media (max-width: 640px) {
    aside {
      width: 100%;
      transition: transform 0.3s ease-in-out;
    }
    aside.w-72 {
      transform: translateX(0);
    }
    aside.w-0 {
      width: 0;
      overflow: hidden;
    }
  }
</style>

<aside 
  x-cloak  
  class="fixed left-0 top-0 h-full bg-white border-r shadow-sm z-40 flex flex-col transition-transform duration-300"
  x-data="{
    isOpen: localStorage.getItem('sidebarOpen') === 'true' || localStorage.getItem('sidebarOpen') === null,
    isDesktop: window.innerWidth >= 640,
    currentPath: window.location.pathname
  }"
  x-init="
    $watch('isOpen', value => {
      localStorage.setItem('sidebarOpen', value);
      $dispatch('sidebar-toggled', { isOpen: value });
    });
    $dispatch('sidebar-toggled', { isOpen: isOpen });
    
    // Real-time resize listener
    $watch('isDesktop', (newVal) => {
      if (!newVal) {
        isOpen = false;
        $dispatch('sidebar-toggled', { isOpen: false });
      }
    });
  "
  @resize.window.debounce.250ms="isDesktop = window.innerWidth >= 640"
  @sidebar-toggled.window="isOpen = $event.detail.isOpen"
  :class="{ 
    'w-72': isOpen, 
    'w-16': !isOpen && isDesktop, 
    'w-0 -translate-x-full': !isOpen && !isDesktop,
    'translate-x-0': isOpen,
    '-translate-x-full hidden': !isOpen && !isDesktop
  }"
  aria-label="Sidebar Admin"
  style="scrollbar-gutter: stable;"
>
  <!-- Sidebar Header -->
  <div 
    class="flex pt-4"
    :class="{ 'px-8 items-center gap-3': isOpen, 'px-2 justify-center': !isOpen }"
  >
    <div class="flex-1 text-left min-w-0" x-show="isOpen" x-cloak>
      <div class="text-xs text-gray-500">Selamat datang,</div>
      <div class="text-lg font-semibold text-gray-900 truncate">
        {{ Auth::check() ? Auth::user()->nama_lengkap : 'Guest' }}
      </div>
    </div>
    <div class="relative group">
      <button 
        @click="isOpen = !isOpen; $dispatch('sidebar-toggled', { isOpen: isOpen })"
        class="p-2 rounded hover:bg-blue-50 flex items-center justify-center"
      >
        <img 
          :src="isOpen ? '{{ asset('img/icon/iconCloseSidebar.png') }}' : '{{ asset('img/icon/iconOpenSidebar.png') }}'"
          alt="Toggle Sidebar" 
          class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]"
        >
      </button>
      <span 
        x-show="!isOpen && isDesktop" 
        x-cloak
        class="absolute left-full top-1/2 -translate-y-1/2 ml-2 whitespace-nowrap bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-0 translate-x-2 pointer-events-none transition-all duration-150 z-50"
      >
        <span x-text="isOpen ? 'Close Sidebar' : 'Open Sidebar'"></span>
      </span>
    </div>
  </div>

  <hr class="mx-4 my-2 border-gray-200" x-show="isOpen" x-cloak>

  <!-- Quick links under header -->
  <div class="p-4 space-y-2" :class="{ 'px-2': !isOpen }" x-show="isOpen || isDesktop">
    @php
      $dashboardRoute = route('dashboard-admin');
      $isDashboardActive = request()->is('admin/dashboard*') || request()->routeIs('dashboard-admin');
    @endphp

    <a 
      href="{{ $dashboardRoute ?? '#' }}" 
      class="flex items-center gap-3 px-2 py-2 rounded-lg relative group transition-colors"
      :class="{
        'justify-center': !isOpen && isDesktop,
        'bg-blue-50 text-blue-700': '{{ $isDashboardActive }}' === '1',
        'hover:bg-blue-50 text-gray-700': '{{ $isDashboardActive }}' !== '1'
      }"
    >
      <img src="{{ asset('img/icon/iconHome.png') }}" alt="Icon Home" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
      <span class="text-sm" x-show="isOpen" x-cloak>Dashboard</span>
      <span 
        x-show="!isOpen && isDesktop" 
        x-cloak
        class="absolute left-full ml-2 bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-10"
      >Dashboard</span>
    </a>

    <a 
      href="{{ $dashboardRoute ?? '#' }}" 
      class="flex items-center gap-3 px-2 py-2 rounded-lg relative group transition-colors"
      :class="{
        'justify-center': !isOpen && isDesktop,
        'bg-blue-50 text-blue-700': '{{ $isDashboardActive }}' === '1',
        'hover:bg-blue-50 text-gray-700': '{{ $isDashboardActive }}' !== '1'
      }"
    >
      <img src="{{ asset('img/icon/iconLaporan.png') }}" alt="Icon Laporan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
      <span class="text-sm" x-show="isOpen" x-cloak>Laporan</span>
      <span 
        x-show="!isOpen && isDesktop" 
        x-cloak
        class="absolute left-full ml-2 bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-10"
      >Laporan</span>
    </a>
  </div>

  <hr class="mx-4 my-2 border-gray-200" x-show="isOpen" x-cloak>

  <!-- Sidebar Body -->
  <div class="flex-1 overflow-y-auto" x-show="isOpen || isDesktop">
    <nav class="p-4 space-y-2" aria-label="Main navigation" :class="{ 'px-2': !isOpen && isDesktop }">
      <div class="text-xs font-semibold text-gray-500 uppercase px-2" x-show="isOpen" x-cloak>Main</div>

      <!-- Penjualan -->
      @php $penjualanActive = request()->routeIs('admin.penjualan.*'); @endphp
      <a 
        href="{{ route('admin.penjualan.index') ?? '#' }}"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $penjualanActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$penjualanActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconBarang.png') }}" alt="Icon Barang" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Penjualan</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          Penjualan
        </span>
      </a>

      <!-- Pembelian -->
      @php $pembelianActive = request()->routeIs('admin.pembelian.*'); @endphp
      <a 
        href="{{ route('admin.pembelian.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $pembelianActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$pembelianActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconSupplier.png') }}" alt="Icon Supplier" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Pembelian</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          Pembelian
        </span>
      </a>

      <div class="h-2" x-show="isOpen"></div>

      <div class="text-xs font-semibold text-gray-500 uppercase px-2" x-show="isOpen">Others</div>

      <!-- Daftar Barang -->
      @php $barangActive = request()->routeIs('admin.data-barang.*'); @endphp
      <a 
        href="{{ route('admin.data-barang.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $barangActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$barangActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconBarang.png') }}" alt="Icon Barang" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Daftar Barang</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          Daftar Barang
        </span>
      </a>
    </nav>
  </div>

  <!-- Sidebar Footer -->
  <div class="border-t p-2" :class="{ 'px-2 ml-4': !isOpen }" x-show="isOpen || isDesktop">
    @php
        $user = Auth::user();
        $foto = $user && $user->foto_user ? asset('storage/' . $user->foto_user) : asset('img/icon/iconProfil.png');
    @endphp
    <div x-data="{ open: false }" x-cloak class="relative">
        <div class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-50 focus:outline-none" :class="{ 'justify-center': !isOpen && isDesktop }">
            <div class="relative h-8 w-8 mx-2 rounded-full overflow-hidden ring-2 ring-gray-200 flex-shrink-0">
                <img src="{{ $foto }}" alt="Profile" class="h-full w-full object-cover" onerror="this.src='{{ asset('img/icon/iconProfil.png') }}'">
            </div>
            <div class="min-w-0 flex-1 text-left ml-2" x-show="isOpen" x-cloak>
                <div class="truncate text-sm font-medium text-gray-900">{{ $user ? $user->username : 'Guest' }}</div>
                <div class="truncate text-xs text-gray-500">{{ $user && $user->role ? ucfirst($user->role->nama_role) : 'Role Tidak Dikenal' }}</div>
            </div>
            <button 
                @click="open = !open"
                class="flex-shrink-0 focus:outline-none"
                x-tooltip="!isOpen && isDesktop ? '{{ $user ? $user->username : 'Guest' }}' : ''"
            >
                <svg x-show="!open && isOpen" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 8l4 4 4-4"/>
                </svg>
                <svg x-show="open && isOpen" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12l4-4 4 4"/>
                </svg>
            </button>
        </div>
        <div 
            x-show="open && isOpen" 
            x-cloak 
            style="display: none;"
            x-transition 
            @click.outside="open = false" 
            class="absolute left-0 bottom-12 w-full bg-white border rounded shadow-md overflow-hidden z-10"
        >
            <a 
                href="{{ route('profile.profile.edit') }}" 
                class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50"
                x-tooltip="!isOpen && isDesktop ? 'My profile' : ''"
            >
                <img src="{{ asset('img/icon/iconSettingProfile.png') }}" alt="My Profile" class="h-4 w-4 text-gray-600">
                <span class="text-sm text-gray-700">My profile</span>
            </a>
            <a 
                href="{{ route('dashboard-master') ?? '#' }}" 
                class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50"
                x-tooltip="!isOpen && isDesktop ? 'Settings' : ''"
            >
                <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l-.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09c.34.06.66.22.93.46"/>
                </svg>
                <span class="text-sm text-gray-700">Settings</span>
            </a>
            <div class="border-t"></div>
            <a 
                href="{{ route('privacy-policy') ?? '#' }}" 
                class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50"
                x-tooltip="!isOpen && isDesktop ? 'Privacy Policy' : ''"
            >
                <img src="{{ asset('img/icon/iconPrivacyPolicy.png') }}" alt="Privacy Policy" class="h-4 w-4 text-gray-600">
                <span class="text-sm text-gray-700">Privacy policy</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button 
                    type="submit" 
                    class="w-full flex items-center gap-3 px-3 py-2 hover:bg-gray-50 text-left"
                    x-tooltip="!isOpen && isDesktop ? 'Sign Out' : ''"
                >
                    <img src="{{ asset('img/icon/iconSignOut.png') }}" alt="Sign Out" class="h-4 w-4 text-gray-600">
                    <span class="text-sm text-gray-700">Sign out</span>
                </button>
            </form>
        </div>
    </div>
  </div>
</aside>