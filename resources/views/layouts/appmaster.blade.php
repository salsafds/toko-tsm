<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Koperasi Tunas Sejahtera Mandiri')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
  [x-cloak] {
    display: none !important;
  }
  .sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 30;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    pointer-events: none;
  }
  .sidebar-overlay.show {
    opacity: 1;
    pointer-events: auto;
  }
  .content-disabled {
    pointer-events: none;
    opacity: 0.7;
  }
  @media (max-width: 640px) {
    header, main, footer {
      width: 100%;
      max-width: 100vw;
      box-sizing: border-box;
    }
    body {
      max-width: 100vw;
      overflow-x: hidden;
    }
    aside {
      width: 100%;
      max-width: 100vw;
      box-sizing: border-box;
    }
    .container {
      max-width: 100vw;
      padding-left: 1rem;
      padding-right: 1rem;
    }
  }
</style>
</head>
<body 
  class="bg-gray-50" 
  x-data="{
    isOpen: window.innerWidth >= 640 ? (localStorage.getItem('sidebarOpen') === 'true' || localStorage.getItem('sidebarOpen') === null) : false,
    isDesktop: window.innerWidth >= 640
  }"
  x-init="
    $dispatch('sidebar-toggled', { isOpen: isOpen });
    console.log('Body initialized, isOpen:', isOpen, 'isDesktop:', isDesktop);
  "
  @resize.window.debounce.250ms="isDesktop = window.innerWidth >= 640"
  @sidebar-toggled.window="isOpen = $event.detail.isOpen; console.log('Body received sidebar-toggled, isOpen:', isOpen)"
>
  {{-- Overlay for Mobile --}}
  <div 
    x-cloak
  x-show.transition.opacity.duration.300ms="isOpen && !isDesktop"
  @click="isOpen = false; $dispatch('sidebar-toggled', { isOpen: false }); localStorage.setItem('sidebarOpen', false); console.log('Overlay clicked, sidebar closed')"
  class="sidebar-overlay"
  :class="{ 'show': isOpen && !isDesktop }"
  ></div>

  {{-- Header --}}
  <header 
    x-cloak 
    class="fixed top-0 right-0 left-0  transition-all duration-300 z-20"
    :style="isDesktop ? { 'margin-left': isOpen ? '18rem' : '4rem' } : { 'margin-left': '0' }"
  >
      @include('layouts.partials.header')

  </header>

  {{-- Sidebar partial --}}
  @include('layouts.partials.sidebarmaster')

  {{-- Konten halaman --}}
  <main 
  x-cloak 
  class="pt-20 px-6 transition-all duration-300 z-10"
  :style="isDesktop
    ? {
        'margin-left': isOpen ? '18rem' : '8rem',
        'margin-right': isOpen ? '0' : '4rem'
      }
    : {
        'margin-left': '0',
        'margin-right': '0'
      }"
>
  @yield('content')
</main>

  {{-- Footer --}}
  <footer 
    x-cloak 
    class="p-2 transition-all duration-300 z-0"
    :style="isDesktop ? { 'margin-left': isOpen ? '18rem' : '4rem' } : { 'margin-left': '0' }"
  >
    @include('layouts.partials.footer')
  </footer>
  @yield('scripts')
  @stack('js')
</body>
</html>