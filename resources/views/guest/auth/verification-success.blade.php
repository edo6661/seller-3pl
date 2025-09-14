<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terverifikasi - Pusat Kirim</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 50px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 40px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .login-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            color: white;
            text-decoration: none;
        }

        .features {
            margin-top: 40px;
            text-align: left;
        }

        .features h3 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 15px;
            text-align: center;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 8px 0;
            color: #666;
            position: relative;
            padding-left: 30px;
        }

        .feature-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #4CAF50;
            font-weight: bold;
            font-size: 16px;
        }

        @media (max-width: 480px) {
            .success-container {
                padding: 40px 30px;
            }

            h1 {
                font-size: 24px;
            }

            .success-icon {
                width: 70px;
                height: 70px;
                font-size: 35px;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1>Email Berhasil Diverifikasi!</h1>
        
        <p>Selamat! Akun Anda telah berhasil diverifikasi. Sekarang Anda dapat mengakses semua fitur platform kami.</p>
        
        <a href="/auth/login" class="login-button">
            <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
            Masuk ke Akun Saya
        </a>
        
        <div class="features">
            <h3>Apa yang bisa Anda lakukan sekarang:</h3>
            <ul class="feature-list">
                <li>Mengelola profil seller Anda</li>
                <li>Mengupload dan mengelola produk</li>
                <li>Melakukan request pickup barang</li>
                <li>Mengakses dashboard analitik</li>
                <li>Menerima notifikasi penting</li>
            </ul>
        </div>
    </div>
</body>
</html>