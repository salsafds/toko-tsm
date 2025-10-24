@extends('layouts.appmaster')

@section('title', 'Privacy Policy')

@section('content')
<div class="container mx-auto px-6 py-10">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Kebijakan Privasi</h1>
    <p class="text-gray-600 mb-8">
        Terakhir diperbarui: 24 Oktober 2025 <br>
        Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.
    </p>

    <div x-data="{ openSection: null }" class="space-y-4">
        {{-- Bagian 1 --}}
        <div class="border border-gray-200 rounded-lg">
            <button 
                @click="openSection === 1 ? openSection = null : openSection = 1"
                class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 rounded-t-lg text-gray-800 font-medium">
                <span>1. Informasi yang Kami Kumpulkan</span>
                <svg :class="openSection === 1 ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSection === 1" x-collapse class="px-5 py-4 text-sm text-gray-700 bg-white">
                Kami dapat mengumpulkan informasi pribadi seperti nama, alamat email, nomor telepon, dan aktivitas penggunaan saat Anda menggunakan situs kami.
            </div>
        </div>

        {{-- Bagian 2 --}}
        <div class="border border-gray-200 rounded-lg">
            <button 
                @click="openSection === 2 ? openSection = null : openSection = 2"
                class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 rounded-t-lg text-gray-800 font-medium">
                <span>2. Cara Kami Menggunakan Informasi Anda</span>
                <svg :class="openSection === 2 ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSection === 2" x-collapse class="px-5 py-4 text-sm text-gray-700 bg-white">
                Informasi digunakan untuk menyediakan layanan, meningkatkan pengalaman pengguna, serta mengirimkan pembaruan terkait produk atau kebijakan kami.
            </div>
        </div>

        {{-- Bagian 3 --}}
        <div class="border border-gray-200 rounded-lg">
            <button 
                @click="openSection === 3 ? openSection = null : openSection = 3"
                class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 rounded-t-lg text-gray-800 font-medium">
                <span>3. Perlindungan Data</span>
                <svg :class="openSection === 3 ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSection === 3" x-collapse class="px-5 py-4 text-sm text-gray-700 bg-white">
                Kami menerapkan langkah-langkah keamanan seperti enkripsi dan kontrol akses untuk melindungi data Anda dari akses tidak sah.
            </div>
        </div>

        {{-- Bagian 4 --}}
        <div class="border border-gray-200 rounded-lg">
            <button 
                @click="openSection === 4 ? openSection = null : openSection = 4"
                class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 rounded-t-lg text-gray-800 font-medium">
                <span>4. Hak Anda</span>
                <svg :class="openSection === 4 ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSection === 4" x-collapse class="px-5 py-4 text-sm text-gray-700 bg-white">
                Anda memiliki hak untuk mengakses, memperbarui, atau menghapus data pribadi Anda dengan menghubungi kami.
            </div>
        </div>

        {{-- Bagian 5 --}}
        <div class="border border-gray-200 rounded-lg">
            <button 
                @click="openSection === 5 ? openSection = null : openSection = 5"
                class="w-full flex justify-between items-center px-5 py-3 bg-gray-50 hover:bg-gray-100 rounded-t-lg text-gray-800 font-medium">
                <span>5. Perubahan pada Kebijakan Ini</span>
                <svg :class="openSection === 5 ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSection === 5" x-collapse class="px-5 py-4 text-sm text-gray-700 bg-white">
                Kami dapat memperbarui kebijakan ini dari waktu ke waktu. Perubahan akan dipublikasikan di halaman ini.
            </div>
        </div>
    </div>

    <div class="mt-10 text-sm text-gray-500">
        <p>Jika Anda memiliki pertanyaan tentang kebijakan privasi ini, silakan hubungi kami melalui menu <strong>Contact Us</strong>.</p>
    </div>
</div>
@endsection
