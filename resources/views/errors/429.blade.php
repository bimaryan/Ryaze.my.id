@extends('errors.layout')
@section('title', 'Too Many Requests')
@section('icon')
    <i class="fa-solid fa-gauge-high text-3xl text-indigo-500 dark:text-indigo-400"></i>
@endsection
@section('code', '429')
@section('message', 'Terlalu Banyak Permintaan')
@section('description', 'Kamu mengakses halaman ini terlalu cepat. Santai dulu sebentar — sistem butuh napas. Coba lagi dalam beberapa detik.')
