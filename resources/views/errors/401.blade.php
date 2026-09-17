@extends('errors.layout')
@section('title', 'Unauthorized')
@section('icon')
    <i class="fa-solid fa-lock text-4xl text-indigo-500 dark:text-indigo-400 -rotate-3"></i>
@endsection
@section('code', '401')
@section('message', 'Tidak Terotentikasi')
@section('description', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.')
