@extends('layouts.main')

@section('title', 'Pagina Principal')

@section('content')
    <div class="main_container">
        <div class="logo">
            <img src="{{ asset('img/laravel.svg') }}" alt="Logo" class="logo_laravel">
        </div>
        <h1 class="titulo_principal">Bienvenido a Laramin Framework, inspirado en el framework basado en PHP de Laravel</h1>
    </div>
@endsection
