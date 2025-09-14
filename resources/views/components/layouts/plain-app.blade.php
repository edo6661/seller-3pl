<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-layouts.head />

    <body>
        <x-shared.header/>
        {{-- <main class="min-h-screen">
            {{ $slot }}
        </main> --}}
        <main class="lg:ml-64 min-h-screen bg-neutral-50">
            <!-- Spacer untuk mobile header -->
            <div class="lg:hidden h-16"></div>
            
            <!-- Container untuk content -->
            <div class="">
                {{ $slot }}
            </div>
        </main>
        {{-- @auth
            <x-shared.chat />
        @endauth --}}
        <x-shared.footer/>
    </body>
</html>
