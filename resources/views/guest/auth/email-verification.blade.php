<x-layouts.plain-app>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2>Hai {{ $user->name }},</h2>
            <p>Terima kasih telah mendaftar di {{ config('app.name') }}!</p>
            <p>Untuk melengkapi proses registrasi, silakan klik tombol di bawah ini untuk memverifikasi email Anda:</p>
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="button">Verifikasi Email</a>
            </p>
            <p>Jika tombol di atas tidak berfungsi, Anda dapat menyalin dan menempelkan URL berikut ke browser Anda:</p>
            <p style="word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 5px;">
                {{ $verificationUrl }}
            </p>
            <p><strong>Catatan:</strong> Link verifikasi ini akan kedaluwarsa dalam 60 menit.</p>
        </div>
        <div class="footer">
            <p>Jika Anda tidak membuat akun di {{ config('app.name') }}, abaikan email ini.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>
    </div>

</x-layouts.plain-app>