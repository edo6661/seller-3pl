<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Permintaan Penarikan Saldo</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .info-card { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #4F46E5; }
        .amount { font-size: 24px; font-weight: bold; color: #4F46E5; }
        .bank-details { background: #e0e7ff; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
        .warning { background: #fef3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏦 Permintaan Penarikan Saldo</h1>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>
            
            <p>Permintaan penarikan saldo Anda telah berhasil dibuat dan sedang menunggu persetujuan admin.</p>
            
            <div class="info-card">
                <h3>📋 Detail Penarikan</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Kode Penarikan:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ $withdrawRequest->withdrawal_code }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Jumlah Penarikan:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;" class="amount">{{ $withdrawRequest->formatted_amount }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Biaya Admin:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ $withdrawRequest->formatted_admin_fee }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;"><strong>Jumlah Diterima:</strong></td>
                        <td style="padding: 8px 0; text-align: right; font-weight: bold; color: #16a085;">{{ $withdrawRequest->formatted_net_amount }}</td>
                    </tr>
                </table>
            </div>

            <div class="bank-details">
                <h4>🏧 Detail Rekening Tujuan</h4>
                <p><strong>Bank:</strong> {{ $withdrawRequest->bank_name }}</p>
                <p><strong>Nomor Rekening:</strong> {{ $withdrawRequest->account_number }}</p>
                <p><strong>Nama Pemilik:</strong> {{ $withdrawRequest->account_name }}</p>
            </div>

            <div class="warning">
                <p><strong>⏰ Informasi Penting:</strong></p>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Dana akan diproses dalam <strong>1-3 hari kerja</strong></li>
                    <li>Pastikan data rekening yang Anda berikan sudah benar</li>
                    <li>Anda akan mendapat notifikasi email saat status berubah</li>
                    <li>Saldo telah langsung dipotong dari dompet Anda</li>
                </ul>
            </div>

            <p>Tanggal Permintaan: <strong>{{ $withdrawRequest->requested_at->format('d F Y, H:i') }} WIB</strong></p>
            
            <div class="footer">
                <p>Terima kasih telah menggunakan layanan kami!</p>
                <p style="color: #999;">Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            </div>
        </div>
    </div>
</body>
</html>
