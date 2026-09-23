@extends('errors.layout')
@section('title', 'Page Expired')
@section('icon')
    <i class="fa-solid fa-rotate text-3xl text-indigo-500 dark:text-indigo-400"></i>
@endsection
@section('code', '419')
@section('message', 'Sesi Kedaluwarsa')
@section('description', 'Token keamanan halaman ini sudah habis masa berlakunya. Muat ulang halaman dan coba lagi — data kamu aman.')
