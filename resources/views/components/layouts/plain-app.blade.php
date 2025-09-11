<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-layouts.head />

    <body>
        <x-shared.header/>
        <main class="min-h-screen">
            {{ $slot }}
        </main>
        {{-- @auth
            <x-shared.chat />
        @endauth --}}
        <x-shared.footer/>
    </body>
</html>
