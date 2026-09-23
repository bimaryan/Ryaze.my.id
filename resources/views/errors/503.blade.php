@extends('errors.layout')
@section('title', 'Service Unavailable')
@section('icon')
    <i class="fa-solid fa-wrench text-3xl text-indigo-500 dark:text-indigo-400"></i>
@endsection
@section('code', '503')
@section('message', 'Sedang Maintenance')
@section('description', 'Sistem sedang dalam pemeliharaan terjadwal untuk meningkatkan performa. Kami akan kembali sebentar lagi — terima kasih atas kesabarannya.')
