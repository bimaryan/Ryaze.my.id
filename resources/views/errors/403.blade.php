@extends('errors.layout')
@section('title', 'Access Forbidden')
@section('icon')
    <i class="fa-solid fa-shield-halved text-4xl text-indigo-500 dark:text-indigo-400 -rotate-3"></i>
@endsection
@section('code', '403')
@section('message', 'Akses Ditolak')
@section('description', 'Anda tidak memiliki izin untuk mengakses halaman ini. Hubungi administrator jika Anda merasa ini adalah kesalahan.')
