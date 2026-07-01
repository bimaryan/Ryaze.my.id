@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Dashboard Joki Code" 
            subtitle="Pantau status pesanan dan proyek terbaru Anda di sini." 
            icon="fa-solid fa-gauge">
            <x-slot:actions>
                <a href="{{ route('user_joki.create') }}"
                    class="inline-flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    + Pesan Joki Baru
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="mt-6">
        </div>
    </x-ui.page-layout>
@endsection
