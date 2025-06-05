<x-layouts.plain-app>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2>Hai {{ $user->name }},</h2>
            <p>Kami menerima permintaan untuk mereset password akun Anda.</p>
            <p>Klik tombol di bawah ini untuk membuat password baru:</p>
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </p>
            <p>Jika tombol di atas tidak berfungsi, Anda dapat menyalin dan menempelkan URL berikut ke browser Anda:</p>
            <p style="word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 5px;">
                {{ $resetUrl }}
            </p>
            <p><strong>Catatan:</strong> Link reset password ini akan kedaluwarsa dalam 60 menit.</p>
            <p>Jika Anda tidak meminta reset password, abaikan email ini. Password akun Anda tidak akan berubah.</p>
        </div>
        <div class="footer">
            <p>Untuk keamanan akun Anda, jangan bagikan link ini kepada siapa pun.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>
    </div>

</x-layouts.plain-app>