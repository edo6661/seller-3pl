<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Status Penarikan Saldo</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .header.success { background: #16a085; }
        .header.processing { background: #3498db; }
        .header.failed { background: #e74c3c; }
        .header.cancelled { background: #95a5a6; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .status-badge { padding: 8px 16px; border-radius: 20px; display: inline-block; font-weight: bold; }
        .status-success { background: #d4edda; color: #155724; }
        .status-processing { background: #cce7ff; color: #004085; }
        .status-failed { background: #f8d7da; color: #721c24; }
        .status-cancelled { background: #e2e3e5; color: #383d41; }
        .info-card { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header {{ match($newStatus) {
            'completed' => 'success',
            'processing' => 'processing', 
            'failed' => 'failed',
            'cancelled' => 'cancelled',
            default => 'processing'
        } }}">
            <h1>{{ match($newStatus) {
                'completed' => '✅ Penarikan Berhasil',
                'processing' => '⏳ Penarikan Sedang Diproses',
                'failed' => '❌ Penarikan Gagal',
                'cancelled' => '🚫 Penarikan Dibatalkan',
                default => '📄 Update Status Penarikan'
            } }}</h1>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>
            
            <p>Status permintaan penarikan saldo Anda telah diperbarui.</p>
            
            <div class="info-card">
                <h3>📋 Detail Penarikan</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Kode Penarikan:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ $withdrawRequest->withdrawal_code }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Jumlah:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ $withdrawRequest->formatted_net_amount }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Bank Tujuan:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ $withdrawRequest->bank_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Status:</strong></td>
                        <td style="padding: 8px 0; text-align: right;">
                            <span class="status-badge status-{{ match($newStatus) {
                                'completed' => 'success',
                                'processing' => 'processing',
                                'failed' => 'failed', 
                                'cancelled' => 'cancelled',
                                default => 'processing'
                            } }}">
                                {{ match($newStatus) {
                                    'completed' => 'Berhasil Diproses',
                                    'processing' => 'Sedang Diproses',
                                    'failed' => 'Gagal Diproses',
                                    'cancelled' => 'Dibatalkan',
                                    default => ucfirst($newStatus)
                                } }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            @if($newStatus === 'completed')
                <div style="background: #d4edda; padding: 15px; border-radius: 6px; margin: 20px 0;">
                    <p><strong>🎉 Selamat! Dana telah berhasil ditransfer ke rekening Anda.</strong></p>
                    <p>Silakan cek rekening {{ $withdrawRequest->bank_name }} - {{ $withdrawRequest->account_number }} Anda.</p>
                </div>
            @elseif($newStatus === 'processing')
                <div style="background: #cce7ff; padding: 15px; border-radius: 6px; margin: 20px 0;">
                    <p><strong>⏳ Permintaan Anda sedang diproses oleh tim kami.</strong></p>
                    <p>Dana akan segera ditransfer ke rekening Anda. Mohon bersabar menunggu.</p>
                </div>
            @elseif($newStatus === 'failed')
                <div style="background: #f8d7da; padding: 15px; border-radius: 6px; margin: 20px 0;">
                    <p><strong>❌ Maaf, permintaan penarikan Anda gagal diproses.</strong></p>
                    <p>Saldo telah dikembalikan ke dompet Anda.</p>
                    @if($withdrawRequest->admin_notes)
                        <p><strong>Catatan Admin:</strong> {{ $withdrawRequest->admin_notes }}</p>
                    @endif
                </div>
            @elseif($newStatus === 'cancelled')
                <div style="background: #e2e3e5; padding: 15px; border-radius: 6px; margin: 20px 0;">
                    <p><strong>🚫 Permintaan penarikan telah dibatalkan.</strong></p>
                    <p>Saldo telah dikembalikan ke dompet Anda.</p>
                    @if($withdrawRequest->admin_notes)
                        <p><strong>Catatan Admin:</strong> {{ $withdrawRequest->admin_notes }}</p>
                    @endif
                </div>
            @endif

            <p>Waktu Update: <strong>{{ $withdrawRequest->updated_at->format('d F Y, H:i') }} WIB</strong></p>
            
            <div class="footer">
                <p>Terima kasih telah menggunakan layanan kami!</p>
                <p style="color: #999;">Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            </div>
        </div>
    </div>
</body>
</html>
