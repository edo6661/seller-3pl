<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Permintaan Penarikan Saldo</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        
    </style>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 30px;">
            <h1 style="color: #1f2937; margin: 0;">{{ config('app.name') }}</h1>
            <p style="color: #6b7280; margin: 5px 0 0 0;">Undangan Tim</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #1f2937; margin-top: 0;">Hai {{ $teamMember->name }},</h2>
            
            <p style="color: #374151; line-height: 1.6;">
                Selamat! Anda telah diundang oleh <strong>{{ $sellerName }}</strong> untuk bergabung sebagai anggota tim di {{ config('app.name') }}.
            </p>
            
            <div style="background: #eff6ff; padding: 20px; border-radius: 6px; margin: 25px 0;">
                <h3 style="color: #1e40af; margin-top: 0;">Detail Akun Anda:</h3>
                <p style="margin: 8px 0;"><strong>Email:</strong> {{ $teamMember->email }}</p>
                <p style="margin: 8px 0;"><strong>Password Sementara:</strong> <code style="background: #e0e7ff; padding: 4px 8px; border-radius: 4px;">{{ $temporaryPassword }}</code></p>
            </div>
            
            <div style="background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; color: #92400e; font-size: 14px;">
                    <strong>⚠️ Penting:</strong> Segera ganti password ini setelah login pertama kali demi keamanan akun Anda.
                </p>
            </div>
            
            <h3 style="color: #1f2937;">Hak Akses Anda:</h3>
            <ul style="color: #374151; line-height: 1.6;">
                @foreach($teamMember->permissions as $permission)
                    <li>{{ \App\Models\TeamMember::getAvailablePermissions()[$permission] ?? $permission }}</li>
                @endforeach
            </ul>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $loginUrl }}" 
                   style="display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    Login Sekarang
                </a>
            </div>
            
            <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
                Jika tombol di atas tidak berfungsi, Anda dapat mengunjungi: <br>
                <a href="{{ $loginUrl }}" style="color: #3b82f6;">{{ $loginUrl }}</a>
            </p>
        </div>
        
        <div style="text-align: center; margin-top: 30px; color: #9ca3af; font-size: 12px;">
            <p>Jika Anda tidak mengharapkan email ini, silakan abaikan.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('team.accept.form', ['email' => $teamMember->email]) }}" 
            style="display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; margin-right: 10px;">
                Aktivasi Akun
            </a>
            <a href="{{ $loginUrl }}" 
            style="display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">
                Login (Jika Sudah Aktif)
            </a>
        </div>
    </div>
</body>
</html>
