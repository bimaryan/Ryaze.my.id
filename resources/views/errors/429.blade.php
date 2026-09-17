@extends('errors.layout')
@section('title', 'Too Many Requests')
@section('icon')
    <i class="fa-solid fa-gauge-high text-4xl text-indigo-500 dark:text-indigo-400 -rotate-3"></i>
@endsection
@section('code', '429')
@section('message', 'Terlalu Banyak Permintaan')
@section('description', 'Anda mengirim terlalu banyak permintaan dalam waktu singkat. Tunggu beberapa saat lalu coba lagi.')
