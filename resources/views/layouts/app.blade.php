<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tienda Siempre Linda') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="icon" href="{{ asset('assets/images/LOGO_INV_SIEMPRE_LINDA-01.png') }}" type="image/png">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

        <!-- Global Toast Notification -->
        <div x-data="{ show: false, message: '', type: 'success' }"
             x-init="
                $watch('show', value => { if (value) setTimeout(() => $data.show = false, 3000) });

                window.addEventListener('app-notification-success', event => {
                    console.log('Success event received:', event.detail.message);
                    $data.message = event.detail.message;
                    $data.type = 'success';
                    $data.show = true;
                });
                window.addEventListener('app-notification-error', event => {
                    console.log('Error event received:', event.detail.message);
                    $data.message = event.detail.message;
                    $data.type = 'error';
                    $data.show = true;
                });
             "
             style="position: fixed; top: 5rem; right: 1rem; z-index: 50;"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="p-4 rounded-md shadow-lg"
             :class="{ 'bg-green-500 text-white': type === 'success', 'bg-red-500 text-white': type === 'error' }"
             role="alert">
            <span class="font-semibold" x-text="message"></span>
            <button @click="show = false" class="ml-4 text-xl font-bold leading-none">&times;</button>
        </div>

        @livewireScripts

        {{-- Session Flash to Toast Notification Converter --}}
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('app-notification-success', { detail: { message: "{{ session('success') }}" } }));
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('app-notification-error', { detail: { message: "{{ session('error') }}" } }));
                });
            </script>
        @endif

    </body>
</html>
