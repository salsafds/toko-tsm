@extends('layouts.app-admin')

@section('title', 'Data Penjualan')

@section('content')
<div class="container mx-auto">
  <div class="flex flex-col items-start mb-4 sm:mb-6">
    <h1 class="text-2xl sm:text-2xl font-semibold text-gray-800 text-left">Data Penjualan</h1>
    <p class="text-xs sm:text-sm text-gray-500 mt-1 text-left">Daftar penjualan dan informasi transaksi</p>
  </div>

  <div class="flex flex-col items-start mb-4 gap-4 sm:flex-row sm:items-center sm:justify-between">
  <!-- Kiri: Show entries + Periode -->
  <div class="flex flex-wrap items-center gap-4">
    <!-- Show entries -->
    <div class="flex items-center gap-2">
      <form method="GET" action="{{ route('admin.penjualan.index') }}" class="flex items-center gap-2">
        @php
          $currentPerPage = request()->query('per_page', 10);
          $currentPeriode = request()->query('periode', 'all');
          $currentQ = request()->query('q', '');
        @endphp
        <label for="per_page" class="text-xs sm:text-sm text-gray-600">Show</label>
        <select name="per_page" id="per_page" onchange="this.form.submit()" class="rounded-md border text-xs sm:text-sm px-2 py-1">
          <option value="5" {{ $currentPerPage == 5 ? 'selected' : '' }}>5</option>
          <option value="10" {{ $currentPerPage == 10 ? 'selected' : '' }}>10</option>
          <option value="25" {{ $currentPerPage == 25 ? 'selected' : '' }}>25</option>
          <option value="50" {{ $currentPerPage == 50 ? 'selected' : '' }}>50</option>
        </select>
        <input type="hidden" name="periode" value="{{ $currentPeriode }}">
        <input type="hidden" name="q" value="{{ $currentQ }}">
      </form>
    </div>

    <!-- Dropdown Periode -->
    <div class="flex items-center gap-2">
      <form method="GET" action="{{ route('admin.penjualan.index') }}" class="flex items-center gap-2">
        <label for="periode" class="text-xs sm:text-sm text-gray-600">Periode</label>
        <select name="periode" id="periode" onchange="this.form.submit()" class="rounded-md border text-xs sm:text-sm px-2 py-1">
          <option value="all" {{ $currentPeriode == 'all' ? 'selected' : '' }}>Semua</option>
          <option value="7days" {{ $currentPeriode == '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
          <option value="3months" {{ $currentPeriode == '3months' ? 'selected' : '' }}>3 Bulan Terakhir</option>
          <option value="1year" {{ $currentPeriode == '1year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
        </select>
        <input type="hidden" name="per_page" value="{{ $currentPerPage }}">
        <input type="hidden" name="q" value="{{ $currentQ }}">
      </form>
    </div>
  </div>

  <!-- Kanan: Search + Tambah -->
  <div class="flex items-center gap-2 w-full sm:w-auto">
    <form method="GET" action="{{ route('admin.penjualan.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
      <div class="relative border rounded-md w-full sm:w-64">
        <svg class="absolute left-3 top-2.5 h-4 sm:h-5 w-4 sm:w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
        </svg>
        <input name="q" value="{{ $currentQ }}" placeholder="Search…" aria-label="Search"
               class="pl-10 pr-3 py-2 rounded-md border-gray-200 text-xs sm:text-sm w-full" />
        <input type="hidden" name="per_page" value="{{ $currentPerPage }}">
        <input type="hidden" name="periode" value="{{ $currentPeriode }}">
      </div>
    </form>

    <a href="{{ route('admin.penjualan.create') }}" 
       class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-700 text-white rounded-md text-sm hover:bg-blue-800">
      <svg class="h-4 sm:h-5 w-4 sm:w-5 sm:mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      <span class="hidden sm:inline">Tambah</span>
    </a>
  </div>
</div>

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded text-xs sm:text-sm">
      {{ session('success') }}
    </div>
  @endif

  {{-- Table --}}
  <div class="bg-white rounded-lg shadow-sm overflow-x-auto border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 table-auto">
      <thead class="bg-gray-50">
        <tr class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
          <th class="w-24 sm:w-32 px-2 sm:px-4 py-2 sm:py-3 border-r border-gray-200">ID</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">User</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Pembeli</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Jenis Pembeli</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Total Harga</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Jenis Bayar</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Sumber</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Tanggal Order</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Tanggal Selesai</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3">Aksi</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($penjualans as $item)
          @php
            $isSelesai = !is_null($item->tanggal_selesai);
            $namaPembeli = $item->pelanggan ? $item->pelanggan->nama_pelanggan : ($item->anggota ? $item->anggota->nama_anggota : 'N/A');
            $jenisPembeli = $item->pelanggan ? 'Pelanggan' : ($item->anggota ? 'Anggota' : 'N/A');
          @endphp
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r border-gray-100">{{ $item->id_penjualan }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r">{{ $item->user?->nama_lengkap ?? 'N/A' }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r">{{ $namaPembeli }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r text-center">{{ $jenisPembeli }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r text-right">Rp {{ number_format($item->total_harga_penjualan, 0, ',', '.') }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r text-center">{{ ucfirst($item->jenis_pembayaran) }}</td>
            <td class="px-2 sm:px-4 py-2 text-center border-r">
              @if($item->sumber_transaksi == 'toko')
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      Toko
                  </span>
              @else
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                      Marketplace
                  </span>
              @endif
            </td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r text-center">{{ $item->tanggal_order->format('d-m-Y H:i') }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r text-center">
              @php
                $dariMarketplace = $item->sumber_transaksi !== 'toko';
                $bisaEditHapus   = !$isSelesai && !$dariMarketplace;
                $tanggalSelesai  = $isSelesai 
                    ? $item->tanggal_selesai 
                    : ($dariMarketplace ? $item->tanggal_order : null);
              @endphp

              @if($tanggalSelesai)
                <span class="text-green-600 font-medium">
                  {{ $tanggalSelesai->format('d-m-Y H:i') }}
                  @if($dariMarketplace)
                  @endif
                </span>
              @else
                <!-- Hanya transaksi toko yang belum selesai yang bisa ditandai selesai -->
                <form action="{{ route('admin.penjualan.selesai', $item->id_penjualan) }}" method="POST" class="inline">
                  @csrf
                  @method('PATCH')
                  <button type="button" 
                          onclick="confirmAction(this.closest('form'), 'Tandai transaksi ini selesai? Stok akan dikurangi.')"
                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200 transition-colors">
                    Selesai
                  </button>
                </form>
              @endif
            </td>

            <td class="px-2 sm:px-4 py-2 text-center">
              <div class="flex justify-center items-center gap-1 sm:gap-2">
                {{-- Edit --}}
                @if($bisaEditHapus)
                  <a href="{{ route('admin.penjualan.edit', $item->id_penjualan) }}"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200">
                    Edit
                  </a>
                @else
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 cursor-not-allowed" title="Tidak dapat diedit">
                    Edit
                  </span>
                @endif

                {{-- Delete --}}
                @if($bisaEditHapus)
                  <form action="{{ route('admin.penjualan.destroy', $item->id_penjualan) }}" method="POST"
                        onsubmit="return confirm('Hapus transaksi ini?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                            onclick="confirmAction(this.closest('form'), 'Hapus transaksi ini? Data tidak bisa dikembalikan.', 'error')"
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-700 hover:bg-rose-200">
                      Delete
                    </button>
                  </form>
                @else
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 cursor-not-allowed" title="Tidak dapat dihapus">
                    Delete
                  </span>
                @endif

                {{-- Print tetap selalu muncul --}}
                <a href="{{ route('admin.penjualan.print', $item->id_penjualan) }}" target="_blank"
                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 hover:bg-purple-200">
                  Print
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="px-4 py-6 sm:py-8 text-center text-xs sm:text-sm text-gray-500">
                Tidak ada data penjualan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-2">
    <div class="text-xs sm:text-sm text-gray-600">
      @if(isset($penjualans) && $penjualans->total())
        Menampilkan {{ $penjualans->firstItem() }} sampai {{ $penjualans->lastItem() }} dari {{ $penjualans->total() }} data
      @endif
    </div>
    <div>
      @if(isset($penjualans) && method_exists($penjualans, 'links'))
        {{ $penjualans->appends(request()->query())->links('vendor.pagination.custom') }}
      @endif
    </div>
  </div>
</div>

<!-- custom modal -->
<div id="customModal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 hidden transition-opacity duration-300">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="customModalContent">
      <div class="p-5 text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" id="modalIconContainer"></div>
        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Judul</h3>
        <div class="mt-2">
          <p class="text-sm text-gray-500" id="modalMessage">Pesan</p>
        </div>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2" id="modalButtons"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.querySelector('input[name="q"]');
  const tableBody = document.querySelector('tbody');
  const perPageSelect = document.querySelector('#per_page');

  function fetchData() {
    const query = searchInput.value;
    const perPage = perPageSelect.value;

    fetch(`{{ route('admin.penjualan.index') }}?q=${encodeURIComponent(query)}&per_page=${perPage}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const newDoc = parser.parseFromString(html, 'text/html');
      const newTbody = newDoc.querySelector('tbody');
      if (newTbody) {
        tableBody.innerHTML = newTbody.innerHTML;
      }
    })
    .catch(error => console.error('Error:', error));
  }

  if (searchInput) searchInput.addEventListener('keyup', fetchData);

  // Modal Logic
    const modal = document.getElementById('customModal');
    const modalContent = document.getElementById('customModalContent');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalIconContainer = document.getElementById('modalIconContainer');
    const modalButtons = document.getElementById('modalButtons');

    window.openModal = function(title, message, type, onConfirm = null) {
      modalTitle.textContent = title;
      modalMessage.textContent = message;
      
      let iconHtml = ''; let iconColorClass = '';
      if(type === 'error') {
        iconColorClass = 'bg-red-100';
        iconHtml = `<svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
      } else if (type === 'success') {
        iconColorClass = 'bg-green-100';
        iconHtml = `<svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`;
      } else if (type === 'warning') {
        iconColorClass = 'bg-yellow-100';
        iconHtml = `<svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
      }
      modalIconContainer.className = `mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4 ${iconColorClass}`;
      modalIconContainer.innerHTML = iconHtml;

      modalButtons.innerHTML = '';
      if (type === 'warning' || type === 'error' && onConfirm) {
        const btnConfirm = document.createElement('button');
        btnConfirm.className = `w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 ${type === 'error' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'} text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm`;
        btnConfirm.textContent = 'Ya, Lanjutkan';
        btnConfirm.onclick = () => { closeModal(); if(onConfirm) onConfirm(); };
        const btnCancel = document.createElement('button');
        btnCancel.className = "mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm";
        btnCancel.textContent = 'Batal';
        btnCancel.onclick = closeModal;
        modalButtons.appendChild(btnConfirm); modalButtons.appendChild(btnCancel);
      } else {
        const btnOk = document.createElement('button');
        btnOk.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm";
        btnOk.textContent = 'OK';
        btnOk.onclick = closeModal;
        modalButtons.appendChild(btnOk);
      }
      modal.classList.remove('hidden');
      setTimeout(() => { modalContent.classList.remove('scale-95', 'opacity-0'); modalContent.classList.add('scale-100', 'opacity-100'); }, 10);
      modal.classList.add('flex');
    }

    window.closeModal = function() {
      modalContent.classList.remove('scale-100', 'opacity-100'); modalContent.classList.add('scale-95', 'opacity-0');
      setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    window.showNotification = function(msg, type = 'success') { openModal(type === 'error' ? 'Oops!' : 'Berhasil', msg, type); }
    window.confirmAction = function(form, msg, type = 'warning') { openModal('Konfirmasi', msg, type, () => { form.submit(); }); }

    // Check Session
    @if(session('success'))
        showNotification("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showNotification("{{ session('error') }}", 'error');
    @endif
});
</script>
@endsection