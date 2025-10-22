<aside 
x-cloak  
  class="fixed left-0 top-0 h-full bg-white border-r shadow-sm z-40 flex flex-col transition-transform duration-300"
  x-data="{
  isOpen: isDesktop ? localStorage.getItem('sidebarOpen') === 'true' : false,
  isDesktop: isDesktop
}"
  x-init="
  if (isOpen === null && isDesktop) isOpen = true;
  $watch('isOpen', value => {
    localStorage.setItem('sidebarOpen', value);
    $dispatch('sidebar-toggled', { isOpen: value });
    console.log('Sidebar state updated, isOpen:', value);
  });
  console.log('Sidebar initialized, isOpen:', isOpen);
  $dispatch('sidebar-toggled', { isOpen: isOpen });
  
  // Tambahan: Real-time resize listener
  $watch('isDesktop', (newVal) => {
    if (newVal && localStorage.getItem('sidebarOpen') !== 'false') {
      isOpen = true;
    } else if (!newVal) {
      isOpen = false;
    }
    $dispatch('sidebar-toggled', { isOpen: isOpen });
  });
"
  @resize.window.debounce.250ms="isDesktop = window.innerWidth >= 640"
  @sidebar-toggled.window="isOpen = $event.detail.isOpen; console.log('Sidebar received sidebar-toggled, isOpen:', isOpen)"
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
        @click="isOpen = !isOpen; $dispatch('sidebar-toggled', { isOpen: isOpen }); console.log('Toggle button clicked, isOpen:', isOpen)"
        class="p-2 rounded hover:bg-gray-50 flex items-center justify-center"
      >
        <img 
          :src="isOpen ? '{{ asset('img/icon/iconCloseSidebar.png') }}' : '{{ asset('img/icon/iconOpenSidebar.png') }}'"
          alt="Toggle Sidebar" 
          class="h-6 w-6 object-contain min-h-[24px] min-w-[24px]"
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
    <a 
      href="{{ route('dashboard-master') ?? '#' }}" 
      class="flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-50 relative group"
      :class="{ 'justify-center': !isOpen && isDesktop }"
      @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Dashboard link clicked, sidebar closed')) : null"
    >
      <img src="{{ asset('img/icon/iconHome.png') }}" alt="Icon Home" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
      <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Dashboard</span>
      <span 
        x-show="!isOpen && isDesktop" 
        x-cloak
        class="absolute left-full ml-2 bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-10"
      >Dashboard</span>
    </a>
  </div>

  <hr class="mx-4 my-2 border-gray-200" x-show="isOpen" x-cloak>

  <!-- Sidebar Body -->
  <div class="flex-1 overflow-y-auto" x-show="isOpen || isDesktop">
    <nav class="p-4 space-y-2" aria-label="Main navigation" :class="{ 'px-2': !isOpen && isDesktop }">
      <div class="text-xs font-semibold text-gray-500 uppercase px-2" x-show="isOpen" x-cloak>Main</div>
      <a 
        href="{{ route('master.data-role.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Barang link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconBarang.png') }}" alt="Icon Barang" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>CRUD Data Barang</span>
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
          CRUD Data Barang
        </span>
      </a>

      <a 
        href="{{ route('master.data-supplier.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Supplier link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconSupplier.png') }}" alt="Icon Supplier" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>CRUD Data Supplier</span>
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

      <a 
        href="{{ route('dashboard-master') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Pelanggan link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconPelanggan.png') }}" alt="Icon Pelanggan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>CRUD Data Pelanggan</span>
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
          CRUD Data Pelanggan
        </span>
      </a>

      <a 
        href="{{ route('master.data-role.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Karyawan link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconKaryawan.png') }}" alt="Icon Karyawan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>CRUD Data Karyawan</span>
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
          CRUD Data Karyawan
        </span>
      </a>

      <div class="h-2" x-show="isOpen"></div>

      <div class="text-xs font-semibold text-gray-500 uppercase px-2" x-show="isOpen">Data Konfigurasi</div>

      <a 
        href="{{ route('master.data-satuan.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Satuan link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconSatuan.png') }}" alt="Icon Satuan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Data Satuan</span>
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
          Data Satuan
        </span>
      </a>

      <a 
        href="{{ route('master.data-role.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Role link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconRole.png') }}" alt="Icon Role" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Data Role</span>
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
          Data Role
        </span>
      </a>

      <a 
        href="{{ route('master.data-jabatan.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Jabatan link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconJabatan.png') }}" alt="Icon Jabatan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Data Jabatan</span>
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
          Data Jabatan
        </span>
      </a>

      <a 
        href="{{ route('dashboard-master') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Kategori Barang link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconKategoriBarang.png') }}" alt="Icon Kategori Barang" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Data Kategori Barang</span>
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

      <a 
        href="{{ route('master.data-pendidikan.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Pendidikan link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconPendidikan.png') }}" alt="Icon Pendidikan" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Data Pendidikan</span>
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
          Data Pendidikan
        </span>
      </a>

      <a 
        href="{{ route('master.data-negara.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Negara link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconNegara.png') }}" alt="Icon Negara" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Data Negara</span>
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

      <a 
        href="{{ route('master.data-provinsi.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Provinsi link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconProvinsi.png') }}" alt="Icon Provinsi" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Data Provinsi</span>
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

      <a 
        href="{{ route('master.data-kota.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Kota link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconKota.png') }}" alt="Icon Kota" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Data Kota</span>
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
          Data Kota
        </span>
      </a>

      <a 
        href="{{ route('master.data-bahasa.index') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Bahasa link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconBahasa.png') }}" alt="Icon Bahasa" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Data Bahasa</span>
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
          Data Bahasa
        </span>
      </a>

      <a 
        href="{{ route('dashboard-master') ?? '#' }}" 
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 relative group"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('Data Agen Ekspedisi link clicked, sidebar closed')) : null"
      >
        <img src="{{ asset('img/icon/iconAgenEkspedisi.png') }}" alt="Icon Agen Ekspedisi" class="h-5 w-5 object-contain min-h-[20px] min-w-[20px]">
        <span class="text-sm text-gray-700" x-show="isOpen" x-cloak>Data Agen Ekspedisi</span>
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
          Data Agen Ekspedisi
        </span>
      </a>
    </nav>
  </div>

  <!-- Sidebar Footer -->
  <div class="border-t p-4" :class="{ 'px-2': !isOpen }" x-show="isOpen || isDesktop">
    @php
      $user = Auth::user();
      $foto = $user && $user->foto_user ? asset('storage/' . $user->foto_user) : asset('img/icon/iconProfil.png');
    @endphp
    <div x-data="{ open: false }" x-cloak class="relative">
      <button 
        @click="open = !open; console.log('Profile button clicked, dropdown open:', open)"
        class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-50 focus:outline-none"
        :class="{ 'justify-center': !isOpen && isDesktop }"
        x-tooltip="!isOpen && isDesktop ? '{{ $user ? $user->username : 'Guest' }}' : ''"
      >
        <div class="relative h-10 w-10 rounded-full overflow-hidden ring-2 ring-gray-200 flex-shrink-0">
          <img src="{{ $foto }}" alt="Profile" class="h-full w-full object-cover" onerror="this.src='{{ asset('img/icon/iconProfil.png') }}'">
        </div>
        <div class="min-w-0" x-show="isOpen" x-cloak>
          <div class="truncate text-sm font-medium text-gray-900">{{ $user ? $user->username : 'Guest' }}</div>
          <div class="truncate text-xs text-gray-500">{{ $user ? ucfirst($user->role) : 'Role Tidak Dikenal' }}</div>
        </div>
        <svg x-show="!open && isOpen" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 8l4 4 4-4"/>
        </svg>
        <svg x-show="open && isOpen" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12l4-4 4 4"/>
        </svg>
      </button>
      <div 
        x-show="open && isOpen" 
        x-cloak 
        style="display: none;"
        x-transition 
        @click.outside="open = false" 
        class="absolute left-0 bottom-12 w-full bg-white border rounded shadow-md overflow-hidden z-10"
      >
        <a 
          href="{{ route('dashboard-master') ?? '#' }}" 
          class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50"
          @click="isDesktop ? (isOpen = false, $dispatch('sidebar-toggled', { isOpen: false }), localStorage.setItem('sidebarOpen', false), console.log('My profile link clicked, sidebar closed')) : null"
          x-tooltip="!isOpen && isDesktop ? 'My profile' : ''"
        >
          <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11c1.657 0 3-1.343 3-3S17.657 5 16 5s-3 1.343-3 3 1.343 3 3 3zM8 11c1.657 0 3-1.343 3-3S9.657 5 8 5 5 6.343 5 8s1.343 3 3 3z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 20c0-3.314 4.03-6 9-6s9 2.686 9 6"/>
          </svg>
          <span class="text-sm text-gray-700">My profile</span>
        </a>
        <!-- Other footer menu items with same @click logic -->
      </div>
    </div>
  </div>
</aside>

<style>
  [x-cloak] {
    display: none !important;
  }
  aside {
    width: 18rem;
  }
  aside.w-16 {
    width: 4rem;
  }
  aside:not(.w-72) .overflow-y-auto::-webkit-scrollbar {
    display: none;
  }
  aside:not(.w-72) .overflow-y-auto {
    -ms-overflow-style: none;
    scrollbar-width: none;
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