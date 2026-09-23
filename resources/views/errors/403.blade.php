@extends('errors.layout')
@section('title', 'Forbidden')
@section('icon')
    <i class="fa-solid fa-shield-halved text-3xl text-indigo-500 dark:text-indigo-400"></i>
@endsection
@section('code', '403')
@section('message', 'Akses Ditolak')
@section('description', 'Kamu tidak punya izin untuk masuk ke area ini. Kalau kamu merasa ini salah, hubungi administrator.')
