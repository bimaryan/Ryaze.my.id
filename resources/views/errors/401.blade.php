@extends('errors.layout')
@section('title', 'Unauthorized')
@section('icon')
    <i class="fa-solid fa-lock text-3xl text-indigo-500 dark:text-indigo-400"></i>
@endsection
@section('code', '401')
@section('message', 'Autentikasi Diperlukan')
@section('description', 'Kamu belum masuk atau sesi kamu sudah habis. Silakan login terlebih dahulu untuk mengakses halaman ini.')
