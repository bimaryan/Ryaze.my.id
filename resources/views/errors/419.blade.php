@extends('errors.layout')
@section('title', 'Page Expired')
@section('icon')
    <i class="fa-solid fa-clock-rotate-left text-4xl text-indigo-500 dark:text-indigo-400 -rotate-3"></i>
@endsection
@section('code', '419')
@section('message', 'Sesi Habis')
@section('description', 'Sesi halaman ini sudah berakhir karena terlalu lama tidak ada aktivitas. Muat ulang halaman untuk melanjutkan.')
