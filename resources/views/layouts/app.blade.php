<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- 例: .png ファイルを使用する場合 --}}
        {{-- <link rel="icon" href="{{ asset('logo.png') }}" type="image/png"> --}}
        <link rel="icon" href="{{ asset('image/logo.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.global-header')
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
            {{-- ★★★ ここにパンくずリストを追加 ★★★ --}}
            @if (!empty(Breadcrumbs::current()))
                <div class="mb-6"> {{-- マージンを追加してコンテンツと分離 --}}
                    {{ Breadcrumbs::render() }}
                </div>
            @endif
                {{ $slot }}
            </main>
        </div>
    </body>
</html>