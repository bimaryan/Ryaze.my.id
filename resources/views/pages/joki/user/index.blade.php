@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Dashboard Joki Code"
            eyebrow="Layanan Joki · Ringkasan"
            subtitle="Pantau status pesanan dan proyek terbaru Anda di sini."
            icon="fa-solid fa-gauge">
            <x-slot:actions>
                <a href="{{ route('user_joki.create') }}" class="ui-btn-primary font-mono text-[11px] uppercase tracking-[0.15em]">
                    + Pesan Joki Baru
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="mt-6">
        </div>
    </x-ui.page-layout>
@endsection
