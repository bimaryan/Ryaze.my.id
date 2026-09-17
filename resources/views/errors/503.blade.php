@extends('errors.layout')
@section('title', 'Service Unavailable')
@section('icon')
    <i class="fa-solid fa-wrench text-4xl text-indigo-500 dark:text-indigo-400 -rotate-3"></i>
@endsection
@section('code', '503')
@section('message', 'Layanan Tidak Tersedia')
@section('description', 'Server sedang dalam pemeliharaan atau sedang bekerja lebih keras dari biasanya. Silakan kembali beberapa saat lagi.')
