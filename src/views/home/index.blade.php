@extends('layouts.main')

@section('title', 'Pagina Principal')

@section('content')
    <main>
        <section class="main_container">
            <div class="logo">
                <img src="{{ asset('img/laravel.svg') }}" alt="Logo" class="logo_laravel">
            </div>
            <h1 class="titulo_principal text-3xl font-bold">Bienvenido a Laramin Framework, inspirado en el framework basado en PHP de Laravel</h1>
        </section>
        <section class="mx-20 my-8">
            <h3 class="p-8 text-2xl font-bold text-center">¡Novedades!</h3>
            <div class="grid grid-cols-3 gap-4 content-center">
                <x-card>
                    Integracion de TailwindCSS y SCSS
                </x-card>
                <x-card>
                    Soporte para components en Blade
                </x-card>
                <x-card>
                    Motor Blade para vistas
                </x-card>
            </div>

        </section>
    </main>

    
@endsection
