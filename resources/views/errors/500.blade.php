@extends('errors.layout')
@section('title', 'Server Error')
@section('icon')
    <i class="fa-solid fa-triangle-exclamation text-3xl text-indigo-500 dark:text-indigo-400"></i>
@endsection
@section('code', '500')
@section('message', 'Kesalahan Server')
@section('description', 'Ada yang tidak beres di sisi server. Tim teknis sudah diberitahu dan sedang bekerja memperbaikinya. Coba lagi sebentar.')
