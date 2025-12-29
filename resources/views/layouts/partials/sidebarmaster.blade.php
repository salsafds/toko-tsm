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
    -ms-overflow-style: none; // Untuk Internet Explorer dan Edge
    scrollbar-width: none; // Untuk Firefox 
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
    saveScrollPosition() {
      const body = this.$refs.sidebarBody;
      if (body) {
        sessionStorage.setItem('sidebarScroll', body.scrollTop);
      }
    },
    restoreScrollPosition() {
      const body = this.$refs.sidebarBody;
      const saved = sessionStorage.getItem('sidebarScroll');
      if (body && saved !== null) {
        this.$nextTick(() => {
          body.scrollTop = parseInt(saved);
        });
      }
    }
  }"
  x-init="
    $watch('isOpen', value => {
      localStorage.setItem('sidebarOpen', value);
      $dispatch('sidebar-toggled', { isOpen: value });
    });
    $dispatch('sidebar-toggled', { isOpen: isOpen });
    
    $watch('isDesktop', (newVal) => {
      if (!newVal) {
        isOpen = false;
        $dispatch('sidebar-toggled', { isOpen: false });
      }
    });
    restoreScrollPosition();
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
  aria-label="Sidebar Master"
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

{{-- Quick links under header --}}
<div class="p-4 space-y-2" :class="{ 'px-2': !isOpen }" x-show="isOpen || isDesktop">
  @php
    $dashboardMasterActive = request()->routeIs('dashboard-master');
    $isLaporanActive = request()->is('laporan/stok*') || request()->is('laporan/bulanan*');
  @endphp

  {{-- Dashboard --}}
  <a
    href="{{ route('dashboard-master') ?? '#' }}"
    @click="saveScrollPosition()"
    class="flex items-center gap-3 px-2 py-2 rounded-lg relative group transition-colors"
    :class="{
      'justify-center': !isOpen && isDesktop,
      'bg-blue-50 text-blue-700': {{ $dashboardMasterActive ? 'true' : 'false' }},
      'hover:bg-blue-50 text-gray-700': {{ !$dashboardMasterActive ? 'true' : 'false' }}
    }"
  >
    <img src="{{ asset('img/icon/iconHome.png') }}" alt="Icon Home" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
    <span class="text-sm" x-show="isOpen" x-cloak>Dashboard</span>
    <span x-show="!isOpen && isDesktop" x-cloak class="absolute left-full ml-2 bg-gray-800 text-white text-xs rounded-lg py-1 px-2 hidden group-hover:block z-10">Dashboard</span>
  </a>

  {{-- Laporan dengan Submenu --}}
  <div x-data="{ laporanOpen: {{ $isLaporanActive ? 'true' : 'false' }} }">
    <a
      href="javascript:void(0)"
      @click="laporanOpen = !laporanOpen; saveScrollPosition()"
      class="flex items-center gap-3 px-2 py-2 rounded-lg relative group transition-colors cursor-pointer"
      :class="{
        'justify-center': !isOpen && isDesktop,
        'bg-blue-50 text-blue-700': laporanOpen || {{ $isLaporanActive ? 'true' : 'false' }},
        'hover:bg-blue-50 text-gray-700': !(laporanOpen || {{ $isLaporanActive ? 'true' : 'false' }})
      }"
    >
      <img src="{{ asset('img/icon/iconLaporan.png') }}" alt="Icon Laporan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
      <span class="text-sm" x-show="isOpen" x-cloak>Laporan</span>

      {{-- Panah dropdown --}}
      <svg x-show="isOpen" x-cloak class="w-4 h-4 ml-auto transition-transform duration-200"
           :class="{ 'rotate-90': laporanOpen }"
           fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>

      {{-- Tooltip saat sidebar mini --}}
      <span x-show="!isOpen && isDesktop" x-cloak
            class="absolute left-full ml-2 bg-gray-900 text-white text-xs rounded-lg py-1 px-3 whitespace-nowrap hidden group-hover:block z-10">
        Laporan
      </span>
    </a>

    {{-- Submenu - pakai x-collapse agar animasi smooth & tinggi nggak ganggu scroll --}}
    <div x-show="isOpen && (laporanOpen || {{ $isLaporanActive ? 'true' : 'false' }})"
         x-collapse
         class="mt-1 space-y-1 pl-8 border-l-2 border-gray-200">
      
      {{-- Laporan Bulanan --}}
      <a href="{{ route('master.laporan.bulanan') }}"
         @click="saveScrollPosition()"
         class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors"
         :class="{
           'bg-blue-50 text-blue-700 font-medium': {{ request()->is('laporan/bulanan*') ? 'true' : 'false' }},
           'hover:bg-blue-50 text-gray-600': {{ !request()->is('laporan/bulanan*') }}
         }">
        <span>Laporan Bulanan</span>
      </a>

      {{-- Laporan Mutasi --}}
      <a href="{{ route('master.mutasi.index') }}"
         @click="saveScrollPosition()"
         class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors"
         :class="{
           'bg-blue-50 text-blue-700 font-medium': {{ request()->is('laporan/bulanan*') ? 'true' : 'false' }},
           'hover:bg-blue-50 text-gray-600': {{ !request()->is('laporan/bulanan*') }}
         }">
        <span>Laporan Mutasi</span>
      </a>
    </div>
  </div>
</div>

<hr class="mx-4 my-2 border-gray-200" x-show="isOpen" x-cloak>

  {{-- Sidebar Body --}}
  <div 
    class="flex-1 overflow-y-auto" 
    x-ref="sidebarBody"
    x-show="isOpen || isDesktop"
    x-init="restoreScrollPosition()"
  >
    <nav class="p-4 space-y-2" aria-label="Main navigation" :class="{ 'px-2': !isOpen && isDesktop }">
      <div class="text-xs font-semibold text-gray-500 uppercase px-2" x-show="isOpen" x-cloak>Main</div>

      {{-- CRUD Data Supplier --}}
      @php $supplierActive = request()->routeIs('master.data-supplier.*'); @endphp
      <a 
        href="{{ route('master.data-supplier.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $supplierActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$supplierActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconSupplier.png') }}" alt="Icon Supplier" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>CRUD Data Supplier</span>
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
          CRUD Data Supplier
        </span>
      </a>

      {{-- CRUD Data Pelanggan --}}
      @php $pelangganActive = request()->routeIs('master.data-pelanggan.*'); @endphp
      <a 
        href="{{ route('master.data-pelanggan.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $pelangganActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$pelangganActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconPelanggan.png') }}" alt="Icon Pelanggan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>CRUD Data Pelanggan</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded-lg py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          CRUD Data Pelanggan
        </span>
      </a>

      {{-- CRUD Data Karyawan --}}
      @php $karyawanActive = request()->routeIs('master.data-user.*'); @endphp
      <a 
        href="{{ route('master.data-user.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $karyawanActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$karyawanActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconKaryawan.png') }}" alt="Icon Karyawan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>CRUD Data Karyawan</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded-lg py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          CRUD Data Karyawan
        </span>
      </a>

      <div class="h-2" x-show="isOpen"></div>

      <div class="text-xs font-semibold text-gray-500 uppercase px-2" x-show="isOpen">Data Konfigurasi</div>

      {{-- Data Satuan --}}
      @php $satuanActive = request()->routeIs('master.data-satuan.*'); @endphp
      <a 
        href="{{ route('master.data-satuan.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $satuanActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$satuanActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconSatuan.png') }}" alt="Icon Satuan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Data Satuan</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded-lg py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          Data Satuan
        </span>
      </a>

      {{-- Data Role --}}
      @php $roleActive = request()->routeIs('master.data-role.*'); @endphp
      <a 
        href="{{ route('master.data-role.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $roleActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$roleActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconRole.png') }}" alt="Icon Role" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Data Role</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded-lg py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          Data Role
        </span>
      </a>

      {{-- Data Jabatan --}}
      @php $jabatanActive = request()->routeIs('master.data-jabatan.*'); @endphp
      <a 
        href="{{ route('master.data-jabatan.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $jabatanActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$jabatanActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconJabatan.png') }}" alt="Icon Jabatan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Data Jabatan</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded-lg py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          Data Jabatan
        </span>
      </a>

      {{-- Data Kategori Barang --}}
      @php $kategoriActive = request()->routeIs('master.data-kategori-barang.*'); @endphp
      <a 
        href="{{ route('master.data-kategori-barang.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $kategoriActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$kategoriActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconKategoriBarang.png') }}" alt="Icon Kategori Barang" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Data Kategori Barang</span>
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
          Data Kategori Barang
        </span>
      </a>

      {{-- Data Pendidikan --}}
      @php $pendidikanActive = request()->routeIs('master.data-pendidikan.*'); @endphp
      <a 
        href="{{ route('master.data-pendidikan.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $pendidikanActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$pendidikanActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconPendidikan.png') }}" alt="Icon Pendidikan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Data Pendidikan</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded-lg py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          Data Pendidikan
        </span>
      </a>

      {{-- Data Negara --}}
      @php $negaraActive = request()->routeIs('master.data-negara.*'); @endphp
      <a 
        href="{{ route('master.data-negara.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $negaraActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$negaraActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconNegara.png') }}" alt="Icon Negara" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Data Negara</span>
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
          Data Negara
        </span>
      </a>

      {{-- Data Provinsi --}}
      @php $provinsiActive = request()->routeIs('master.data-provinsi.*'); @endphp
      <a 
        href="{{ route('master.data-provinsi.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $provinsiActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$provinsiActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconProvinsi.png') }}" alt="Icon Provinsi" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Data Provinsi</span>
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
          Data Provinsi
        </span>
      </a>

      {{-- Data Kota --}}
      @php $kotaActive = request()->routeIs('master.data-kota.*'); @endphp
      <a 
        href="{{ route('master.data-kota.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $kotaActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$kotaActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconKota.png') }}" alt="Icon Kota" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Data Kota</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded-lg py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          Data Kota
        </span>
      </a>

      {{-- Data Bahasa --}}
      @php $bahasaActive = request()->routeIs('master.data-bahasa.*'); @endphp
      <a 
        href="{{ route('master.data-bahasa.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $bahasaActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$bahasaActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconBahasa.png') }}" alt="Icon Bahasa" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Data Bahasa</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded-lg py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          Data Bahasa
        </span>
      </a>

      {{-- Data Agen Ekspedisi --}}
      @php $ekspedisiActive = request()->routeIs('master.data-agen-ekspedisi.*'); @endphp
      <a 
        href="{{ route('master.data-agen-ekspedisi.index') ?? '#' }}" 
        @click="saveScrollPosition()"
        class="flex items-center gap-3 px-3 py-2 rounded-lg relative group transition-colors"
        :class="{
          'justify-center': !isOpen && isDesktop,
          'bg-blue-50 text-blue-700': {{ $ekspedisiActive ? 'true' : 'false' }},
          'hover:bg-blue-50 text-gray-700': {{ !$ekspedisiActive ? 'true' : 'false' }}
        }"
      >
        <img src="{{ asset('img/icon/iconAgenEkspedisi.png') }}" alt="Icon Agen Ekspedisi" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm" x-show="isOpen" x-cloak>Data Agen Ekspedisi</span>
        <span 
          x-show="!isOpen && isDesktop" 
          x-cloak
          x-ref="tooltip"
          class="fixed left-[72px] bg-gray-800 text-white text-xs rounded-lg py-1 px-2 hidden group-hover:block z-50 transition-all duration-100"
          x-data="{ updatePosition() { 
            const parentRect = this.$el.parentElement.getBoundingClientRect(); 
            const scrollOffset = window.scrollY;
            this.$el.style.top = (parentRect.top + scrollOffset + (parentRect.height / 2) - (this.$el.offsetHeight / 2)) + 'px'; 
          } }"
          @mouseover.window="updatePosition()"
          @scroll.window="updatePosition()"
          style="transform: translateY(-50%);">
          Data Agen Ekspedisi
        </span>
      </a>
    </nav>
  </div>

  {{-- Sidebar Footer --}}
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