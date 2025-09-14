<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .content h2 {
            color: #2c3e50;
            margin-top: 0;
            font-size: 24px;
            font-weight: 400;
        }
        
        .content p {
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.7;
            color: #555555;
        }
        
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .url-box {
            word-break: break-all;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #667eea;
            font-size: 14px;
            color: #666;
            margin: 20px 0;
        }
        
        .note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 25px 0;
        }
        
        .note strong {
            color: #856404;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #6c757d;
        }
        
         .icon {
            width: 60px;    
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex; 
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto; 
            font-size: 24px;
            line-height: 1; 
        }
        
        
        @media only screen and (max-width: 600px) {
            .container {
                margin: 20px;
                border-radius: 4px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .header {
                padding: 25px 15px;
            }
            
            .button {
                display: block;
                margin: 20px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">✉️</div>
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            <h2>Hai {{ $user->name }},</h2>
            <p>Terima kasih telah mendaftar di <strong>{{ config('app.name') }}</strong>!</p>
            <p>Untuk melengkapi proses registrasi dan mengaktifkan akun Anda, silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda:</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="button">✓ Verifikasi Email Saya</a>
            </div>
            
            <p>Jika tombol di atas tidak berfungsi, Anda dapat menyalin dan menempelkan URL berikut ke browser Anda:</p>
            
            <div class="url-box">
                {{ $verificationUrl }}
            </div>
            
            <div class="note">
                <p><strong>⏰ Penting:</strong> Link verifikasi ini akan kedaluwarsa dalam 60 menit demi keamanan akun Anda.</p>
            </div>
            
            <p>Setelah verifikasi berhasil, Anda akan dapat:</p>
            <ul style="color: #555; line-height: 1.7;">
                <li>✅ Mengakses semua fitur akun</li>
                <li>✅ Mengelola profil seller</li>
                <li>✅ Melakukan transaksi dengan aman</li>
                <li>✅ Menerima notifikasi penting</li>
            </ul>
        </div>
        
        <div class="footer">
            <p><strong>🔐 Keamanan:</strong> Jika Anda tidak membuat akun di {{ config('app.name') }}, abaikan email ini.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>