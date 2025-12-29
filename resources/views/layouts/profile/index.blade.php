@extends(Auth::check() && Auth::user()->id_role === 'R02' ? 'layouts.app-admin' : 'layouts.appmaster')

@section('title', 'Kebijakan Privasi - Koperasi Tunas Sejahtera Mandiri')

@section('content')
<div class="container mx-auto px-6 py-10">
    <div class="bg-white shadow-lg rounded-2xl p-8 max-w-5xl mx-auto">
        <h1 class="text-3xl font-semibold text-gray-800 mb-2 text-center">Kebijakan Privasi</h1>
        <p class="text-gray-500 text-center mb-10">
            Diperbarui pada 24 Oktober 2025 • Koperasi Tunas Sejahtera Mandiri
        </p>

        <div x-data="{ openSections: {1: false, 2: false, 3: false, 4: false, 5: false, 6: false} }" class="space-y-4">

            {{-- Pengenalan --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <button 
                    @click="openSections[1] = !openSections[1]"
                    class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 text-gray-800 font-semibold transition-all">
                    <span>Pengenalan</span>
                    <svg :class="openSections[1] ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openSections[1]" x-collapse class="px-6 py-5 text-sm text-gray-700 leading-relaxed bg-white">
                    <p>Kami di <strong>Koperasi Tunas Sejahtera Mandiri</strong> menghormati dan menjaga privasi setiap anggota, karyawan, dan pengguna sistem kami. 
                    Dokumen ini menjelaskan bagaimana kami mengelola data pribadi yang dikumpulkan, disimpan, digunakan, dan dilindungi ketika Anda menggunakan sistem internal koperasi.</p>

                    <p>Kami memahami pentingnya keamanan data pribadi di era digital saat ini. Karena itu, kami berkomitmen menjaga kerahasiaan seluruh informasi yang kami terima 
                    dan memastikan pengguna memiliki kendali atas data mereka. Kebijakan ini juga menjelaskan bagaimana kami menangani permintaan penghapusan data, pengawasan aktivitas sistem, 
                    serta transparansi dalam setiap proses pengelolaan data.</p>
                </div>
            </div>

            {{-- Informasi yang Kami Kumpulkan --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <button 
                    @click="openSections[2] = !openSections[2]"
                    class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 text-gray-800 font-semibold transition-all">
                    <span>Informasi yang Kami Kumpulkan</span>
                    <svg :class="openSections[2] ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openSections[2]" x-collapse class="px-6 py-5 text-sm text-gray-700 leading-relaxed bg-white space-y-3">
                    <p>Kami mengumpulkan data untuk memberikan pengalaman yang lebih aman, efisien, dan sesuai kebutuhan Anda. Informasi dikumpulkan saat Anda 
                    mengakses sistem, melakukan login, memperbarui data, atau menggunakan fitur tertentu.</p>

                    <p><strong>Jenis data yang kami kumpulkan meliputi:</strong></p>
                    <ul class="list-disc pl-6 space-y-1">
                        <li><strong>Data Identitas:</strong> Nama lengkap, email, nomor anggota, jabatan, dan informasi login (username dan password yang telah di-hash).</li>
                        <li><strong>Data Aktivitas:</strong> Log aktivitas pengguna seperti waktu login, perubahan data, serta riwayat transaksi dalam sistem.</li>
                        <li><strong>Data Teknis:</strong> Alamat IP, jenis perangkat, dan browser yang digunakan untuk menjaga keamanan dan audit sistem.</li>
                    </ul>

                    <p>Kami tidak mengumpulkan data sensitif seperti informasi keuangan pribadi atau data biometrik tanpa izin Anda. 
                    Semua data disimpan dalam sistem internal dengan perlindungan yang sesuai standar keamanan industri.</p>
                </div>
            </div>

            {{-- Penggunaan Informasi --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <button 
                    @click="openSections[3] = !openSections[3]"
                    class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 text-gray-800 font-semibold transition-all">
                    <span>Bagaimana Kami Menggunakan Informasi</span>
                    <svg :class="openSections[3] ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openSections[3]" x-collapse class="px-6 py-5 text-sm text-gray-700 leading-relaxed bg-white space-y-3">
                    <p>Kami menggunakan data Anda untuk memastikan sistem koperasi berjalan dengan lancar dan layanan dapat diberikan dengan akurat. 
                    Penggunaan data ini juga membantu kami memahami kebutuhan pengguna dan meningkatkan pengalaman administrasi serta efisiensi layanan internal.</p>

                    <p><strong>Beberapa cara kami menggunakan informasi Anda antara lain:</strong></p>
                    <ul class="list-disc pl-6 space-y-1">
                        <li>Memverifikasi identitas pengguna untuk menjaga keamanan sistem.</li>
                        <li>Memproses dan mencatat transaksi anggota, seperti simpanan, pinjaman, dan laporan keuangan.</li>
                        <li>Meningkatkan fitur aplikasi dan antarmuka berdasarkan data penggunaan.</li>
                        <li>Mengirimkan notifikasi atau pembaruan terkait aktivitas sistem.</li>
                    </ul>

                    <p>Kami tidak menggunakan data pribadi Anda untuk iklan atau tujuan komersial eksternal. Setiap pemrosesan informasi dilakukan dengan pertimbangan privasi dan 
                    kepatuhan terhadap peraturan perlindungan data yang berlaku.</p>
                </div>
            </div>

            {{-- Keamanan Data --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <button 
                    @click="openSections[4] = !openSections[4]"
                    class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 text-gray-800 font-semibold transition-all">
                    <span>Keamanan Data</span>
                    <svg :class="openSections[4] ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openSections[4]" x-collapse class="px-6 py-5 text-sm text-gray-700 leading-relaxed bg-white space-y-3">
                    <p>Kami menerapkan berbagai langkah keamanan untuk memastikan data Anda terlindungi dari akses tidak sah, penyalahgunaan, atau kebocoran. 
                    Sistem kami dilengkapi dengan firewall, enkripsi SSL, dan metode otentikasi berlapis untuk melindungi data dalam setiap transaksi.</p>

                    <p>Selain itu, setiap aktivitas pengguna dicatat dalam log audit untuk mendeteksi potensi pelanggaran dan memastikan integritas sistem tetap terjaga. 
                    Kami juga melakukan backup data secara rutin dan membatasi akses hanya kepada personel yang berwenang.</p>

                    <p>Namun, meskipun kami berupaya maksimal melindungi data Anda, kami tetap mendorong semua pengguna untuk menggunakan password yang kuat 
                    dan tidak membagikan informasi login kepada pihak lain.</p>
                </div>
            </div>

            {{-- Hak Anda --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <button 
                    @click="openSections[5] = !openSections[5]"
                    class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 text-gray-800 font-semibold transition-all">
                    <span>Hak Anda</span>
                    <svg :class="openSections[5] ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openSections[5]" x-collapse class="px-6 py-5 text-sm text-gray-700 leading-relaxed bg-white space-y-3">
                    <p>Kami percaya setiap pengguna berhak mengetahui bagaimana data mereka digunakan dan memiliki kendali penuh atas informasi pribadi mereka. 
                    Oleh karena itu, kami memberikan hak berikut kepada Anda:</p>

                    <ul class="list-disc pl-6 space-y-1">
                        <li>Mengakses dan meninjau data pribadi yang disimpan oleh sistem kami.</li>
                        <li>Memperbarui atau mengoreksi data yang tidak akurat atau tidak lengkap.</li>
                        <li>Meminta penghapusan data yang tidak lagi diperlukan atau jika ingin berhenti menggunakan layanan.</li>
                        <li>Menolak penggunaan data untuk tujuan tertentu (seperti pengiriman notifikasi).</li>
                    </ul>

                    <p>Untuk menggunakan hak-hak ini, Anda dapat menghubungi kami melalui email resmi <strong>info@koperasitsm.co.id</strong>. 
                    Setiap permintaan akan kami tanggapi secara profesional dengan menjaga keamanan dan privasi data Anda.</p>
                </div>
            </div>

            {{-- Hubungi Kami --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <button 
                    @click="openSections[6] = !openSections[6]"
                    class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 text-gray-800 font-semibold transition-all">
                    <span>Hubungi Kami</span>
                    <svg :class="openSections[6] ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openSections[6]" x-collapse class="px-6 py-5 text-sm text-gray-700 leading-relaxed bg-white space-y-2">
                    <p>Jika Anda memiliki pertanyaan, saran, atau keluhan mengenai kebijakan privasi ini, tim kami siap membantu. 
                    Kami menghargai setiap masukan untuk meningkatkan keamanan dan pengalaman pengguna di lingkungan digital koperasi.</p>
                    <ul class="mt-3 leading-relaxed list-none">
                        <li><strong>Koperasi Tunas Sejahtera Mandiri</strong></li>
                        <li>Jl. Karah Agung 45, Surabaya, Jawa Timur, Indonesia</li>
                        <li><strong>Email:</strong> info@koperasitsm.co.id</li>
                        <li><strong>Telepon:</strong> (031) 123-4567</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
