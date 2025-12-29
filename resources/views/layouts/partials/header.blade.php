<header class="bg-white shadow-sm">
  <div class="container mx-auto px-4 py-3 flex items-center justify-between">
    <!-- Hamburger Button for Mobile -->
    <button 
      x-show="!isDesktop"
      @click="$dispatch('sidebar-toggled', { isOpen: !isOpen }); isOpen = !isOpen; localStorage.setItem('sidebarOpen', isOpen); console.log('Hamburger clicked, isOpen:', isOpen)"
      class="p-2 rounded hover:bg-gray-50 mr-1 z-50"
      style="pointer-events: auto;"
      aria-label="Toggle Sidebar"
    >
      <img 
        src="{{ asset('img/icon/iconHamburger.png') }}" 
        alt="Toggle Sidebar" 
        class="h-5 w-5 object-contain"
      >
    </button>

    {{-- Nama Toko + Alamat --}}
    <div class="flex items-start gap-3">
      <div class="bg-transparent flex items-center">
        <img src="{{ asset('img/logotsm.png') }}" alt="Logo Koperasi"
             class="h-10 w-10 object-contain rounded-md">
      </div>
      <div>
        <div class="font-semibold text-base text-blue-800">
          Koperasi Tunas Sejahtera Mandiri
        </div>
        <div class="text-xs text-gray-600 mt-1">
          Jl. Karah Agung 45, Surabaya, Jawa Timur, Indonesia.
        </div>
      </div>
    </div>
  </div>
</header>