<x-layouts.plain-app>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2>Hai {{ $user->name }},</h2>
            <p>{{ $statusMessage }}</p>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="margin-top: 0;">Detail Transaksi:</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">ID Transaksi:</td>
                        <td style="padding: 8px 0;">{{ $transaction->reference_id }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Jenis Transaksi:</td>
                        <td style="padding: 8px 0;">{{ $transaction->type_label }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Jumlah:</td>
                        <td style="padding: 8px 0;">{{ $transaction->formatted_amount }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Status:</td>
                        <td style="padding: 8px 0;">
                            <span style="
                                padding: 4px 8px; 
                                border-radius: 4px; 
                                font-size: 12px;
                            "
                                class="{{ $transaction->status_color }}"
                            >
                                {{ $transaction->status_label }}
                            </span>
                        </td>
                    </tr>
                    @if($transaction->payment_type)
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Metode Pembayaran:</td>
                        <td style="padding: 8px 0;">{{ strtoupper($transaction->payment_type) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Tanggal:</td>
                        <td style="padding: 8px 0;">{{ $transaction->created_at->format('d F Y, H:i') }} WIB</td>
                    </tr>
                </table>
            </div>

            @if($transaction->status->value === 'success')
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #c3e6cb;">
                    <strong>✅ Transaksi Berhasil!</strong><br>
                    @if($transaction->type->value === 'topup')
                        Saldo Anda telah berhasil ditambahkan dan siap digunakan.
                    @elseif($transaction->type->value === 'withdraw')
                        Permintaan penarikan Anda sedang diproses dan akan ditransfer dalam 1-3 hari kerja.
                    @endif
                </div>
            @elseif($transaction->status->value === 'failed')
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #f5c6cb;">
                    <strong>❌ Transaksi Gagal</strong><br>
                    Silakan coba lagi atau hubungi customer service jika masalah berlanjut.
                </div>
            @elseif($transaction->status->value === 'cancelled')
                <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #ffeaa7;">
                    <strong>⚠️ Transaksi Dibatalkan</strong><br>
                    Transaksi telah dibatalkan. Tidak ada dana yang terpengaruh.
                </div>
            @elseif($transaction->status->value === 'pending')
                <div style="background: #cce5ff; color: #004085; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #b8daff;">
                    <strong>⏳ Transaksi Sedang Diproses</strong><br>
                    Mohon tunggu, transaksi Anda sedang dalam proses verifikasi.
                </div>
            @endif

            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $dashboardUrl }}" class="button">Lihat Dashboard Wallet</a>
            </p>

            <p style="text-align: center; margin: 20px 0;">
                <a href="{{ $transactionDetailUrl }}" style="color: #007bff; text-decoration: none;">
                    Lihat Detail Transaksi
                </a>
            </p>

            <p>Jika Anda memiliki pertanyaan atau memerlukan bantuan, jangan ragu untuk menghubungi customer service kami.</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis. Mohon jangan membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>
    </div>
</x-layouts.plain-app>