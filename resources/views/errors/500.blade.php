@extends('errors.layout')
@section('title', 'Server Error')
@section('icon')
    <i class="fa-solid fa-server text-4xl text-indigo-500 dark:text-indigo-400 -rotate-3"></i>
@endsection
@section('code', '500')
@section('message', 'Kesalahan Server')
@section('description', 'Terjadi kesalahan tak terduga di server kami. Tim teknis sudah mengetahuinya dan sedang menangani.')
