<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <a href="{{ route('seller.wallet.index') }}"
                        class="text-secondary-600 hover:text-secondary-800 mr-4 transition flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dompet
                    </a>
                    <h1 class="text-2xl font-bold text-neutral-900">Detail Transaksi</h1>
                </div>
                
                <!-- Action Buttons for Pending Transactions -->
                @if ($transaction->isPending())
                    <div class="flex space-x-2">
                        @if ($transaction->isTopup())
                            @if (!$transaction->bank_name)
                                <!-- Belum memilih bank -->
                                <a href="{{ route('seller.wallet.topup.payment', $transaction->reference_id) }}"
                                    class="bg-primary-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-primary-700 transition shadow-md hover:shadow-lg flex items-center text-sm">
                                    <i class="fas fa-credit-card mr-2"></i> Pilih Bank
                                </a>
                            @elseif (!$transaction->payment_proof_path)
                                <!-- Belum upload bukti -->
                                <a href="{{ route('seller.wallet.topup.upload', $transaction->reference_id) }}"
                                    class="bg-primary-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-primary-700 transition shadow-md hover:shadow-lg flex items-center text-sm">
                                    <i class="fas fa-upload mr-2"></i> Upload Bukti
                                </a>
                            @endif
                        @endif
                        
                        @if ($transaction->canBeCancelled())
                            <form action="{{ route('seller.wallet.transaction.cancel', $transaction->id) }}" 
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Yakin ingin membatalkan transaksi ini?')">
                                @csrf
                                <button type="submit"
                                    class="bg-error-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-error-700 transition shadow-md hover:shadow-lg flex items-center text-sm">
                                    <i class="fas fa-times mr-2"></i> Batalkan
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
            <p class="text-neutral-600">Informasi lengkap tentang transaksi ini</p>
        </div>

        <!-- Transaction Detail Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Status Header -->
            <div class="px-6 py-4 border-b border-neutral-200 {{ $transaction->type->increasesBalance() ? 'bg-success-50' : 'bg-error-50' }}">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-xl font-semibold text-neutral-900">
                            {{ $transaction->type_label }}
                        </h2>
                        <p class="text-sm text-neutral-500 mt-1">
                            <i class="fas fa-hashtag mr-1"></i> {{ $transaction->reference_id ?? 'TXN-' . $transaction->id }}
                        </p>
                    </div>
                    <div class="text-left md:text-right">
                        <div class="text-2xl font-bold {{ $transaction->type->increasesBalance() ? 'text-success-600' : 'text-error-600' }}">
                            {{ $transaction->formatted_amount }}
                        </div>
                        @if ($transaction->admin_fee > 0)
                            <div class="text-sm text-neutral-500 mt-1">
                                Biaya Admin: {{ $transaction->formatted_admin_fee }}
                            </div>
                            <div class="text-lg font-semibold text-neutral-700 mt-1">
                                Net: {{ $transaction->formatted_net_amount }}
                            </div>
                        @endif
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-2
                            bg-{{ $transaction->status_color }}-100 text-{{ $transaction->status_color }}-700">
                            <i class="fas {{ $transaction->isPending() ? 'fa-clock' : ($transaction->isSuccess() ? 'fa-check-circle' : ($transaction->isFailed() ? 'fa-times-circle' : 'fa-ban')) }} mr-1"></i>
                            {{ $transaction->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Transaction Info -->
            <div class="px-6 py-4 divide-y divide-neutral-200">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-4">
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-neutral-400"></i> Tanggal Dibuat
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    @if ($transaction->requested_at)
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-clock mr-2 text-neutral-400"></i> Tanggal Request
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->requested_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    @endif

                    @if ($transaction->processed_at)
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-cogs mr-2 text-neutral-400"></i> Diproses
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->processed_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    @endif

                    @if ($transaction->completed_at)
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-check-circle mr-2 text-neutral-400"></i> Selesai
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->completed_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Balance Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-4">
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-wallet mr-2 text-neutral-400"></i> Saldo Sebelum
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->formatted_balance_before }}</p>
                    </div>

                    @if ($transaction->balance_after !== $transaction->balance_before || $transaction->isSuccess())
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-wallet mr-2 text-neutral-400"></i> Saldo Sesudah
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->formatted_balance_after }}</p>
                    </div>
                    @endif
                </div>

                <!-- Bank Information -->
                @if ($transaction->isTopup() && $transaction->bank_name)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-4">
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-university mr-2 text-neutral-400"></i> Bank Transfer
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->bank_name }}</p>
                    </div>

                    @if ($transaction->bank_account_number)
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-credit-card mr-2 text-neutral-400"></i> Nomor Rekening
                        </h3>
                        <p class="text-neutral-600 font-mono">{{ $transaction->bank_account_number }}</p>
                    </div>
                    @endif

                    @if ($transaction->bank_account_name)
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-user mr-2 text-neutral-400"></i> Atas Nama
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->bank_account_name }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Withdraw Information -->
                @if ($transaction->isWithdrawal())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-4">
                    @if ($transaction->bank_name)
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-university mr-2 text-neutral-400"></i> Bank Tujuan
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->bank_name }}</p>
                    </div>
                    @endif

                    @if ($transaction->account_number)
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-credit-card mr-2 text-neutral-400"></i> Nomor Rekening
                        </h3>
                        <p class="text-neutral-600 font-mono">{{ $transaction->account_number }}</p>
                    </div>
                    @endif

                    @if ($transaction->account_name)
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-user mr-2 text-neutral-400"></i> Atas Nama
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->account_name }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Description -->
                <div class="pt-4">
                    <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                        <i class="fas fa-align-left mr-2 text-neutral-400"></i> Deskripsi
                    </h3>
                    <p class="text-neutral-600">{{ $transaction->description ?? 'Tidak ada deskripsi' }}</p>
                </div>

                <!-- Admin Notes -->
                @if ($transaction->admin_notes)
                <div class="pt-4">
                    <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-neutral-400"></i> Catatan Admin
                    </h3>
                    <div class="bg-neutral-50 p-3 rounded-lg">
                        <p class="text-neutral-600">{{ $transaction->admin_notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Payment Proof -->
                @if ($transaction->payment_proof_url)
                <div class="pt-4">
                    <h3 class="text-sm font-medium text-neutral-700 mb-3 flex items-center">
                        <i class="fas fa-receipt mr-2 text-neutral-400"></i> Bukti Pembayaran
                    </h3>
                    <div class="bg-neutral-50 p-4 rounded-lg">
                        <img src="{{ $transaction->payment_proof_url }}" 
                             alt="Bukti Pembayaran" 
                             class="max-w-full h-auto max-h-96 rounded-lg shadow-sm cursor-pointer"
                             onclick="window.open(this.src, '_blank')">
                        <p class="text-xs text-neutral-500 mt-2">Klik gambar untuk memperbesar</p>
                    </div>
                </div>
                @endif

                <!-- QR Code (jika ada) -->
                @if ($transaction->qr_code_url)
                <div class="pt-4">
                    <h3 class="text-sm font-medium text-neutral-700 mb-3 flex items-center">
                        <i class="fas fa-qrcode mr-2 text-neutral-400"></i> QR Code Pembayaran
                    </h3>
                    <div class="bg-neutral-50 p-4 rounded-lg text-center">
                        <img src="{{ $transaction->qr_code_url }}" 
                             alt="QR Code" 
                             class="mx-auto h-32 w-32">
                        <p class="text-xs text-neutral-500 mt-2">Scan QR Code untuk pembayaran</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 border-t border-neutral-200 bg-neutral-50">
                <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                    <div class="text-sm text-neutral-500">
                        Terakhir diupdate: {{ $transaction->updated_at->format('d/m/Y H:i:s') }}
                    </div>
                    
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                        <a href="{{ route('seller.wallet.index') }}"
                            class="bg-secondary-600 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-secondary-700 transition shadow-sm hover:shadow-md flex items-center justify-center">
                            <i class="fas fa-wallet mr-2"></i> Kembali ke Dompet
                        </a>
                        
                        @if ($transaction->isSuccess() || $transaction->isFailed())
                            <a href="{{ route('seller.wallet.topup') }}"
                                class="bg-white text-primary-600 border border-primary-200 px-5 py-2.5 rounded-lg font-semibold hover:bg-primary-50 transition shadow-sm hover:shadow-md flex items-center justify-center">
                                <i class="fas fa-plus-circle mr-2"></i> Top Up Lagi
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Process Info -->
        @if ($transaction->isPending())
        <div class="mt-8">
            @if ($transaction->isTopup())
                @if (!$transaction->bank_name)
                    <!-- Belum pilih bank -->
                    <div class="bg-info-50 border border-info-200 rounded-xl p-5">
                        <div class="flex">
                            <div class="flex-shrink-0 pt-0.5">
                                <i class="fas fa-info-circle text-info-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-medium text-info-800">Langkah Selanjutnya</h3>
                                <div class="mt-2 text-sm text-info-700">
                                    <p>Silakan pilih bank untuk melakukan pembayaran top up.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif (!$transaction->payment_proof_path)
                    <!-- Belum upload bukti -->
                    <div class="bg-warning-50 border border-warning-200 rounded-xl p-5">
                        <div class="flex">
                            <div class="flex-shrink-0 pt-0.5">
                                <i class="fas fa-upload text-warning-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-medium text-warning-800">Menunggu Pembayaran</h3>
                                <div class="mt-2 text-sm text-warning-700">
                                    <ul class="list-disc list-inside space-y-1.5">
                                        <li>Transfer sejumlah <strong>{{ $transaction->formatted_amount }}</strong> ke rekening yang tertera</li>
                                        <li>Setelah transfer, upload bukti pembayaran</li>
                                        <li>Admin akan memverifikasi dalam 1x24 jam</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Menunggu verifikasi -->
                    <div class="bg-info-50 border border-info-200 rounded-xl p-5">
                        <div class="flex">
                            <div class="flex-shrink-0 pt-0.5">
                                <i class="fas fa-clock text-info-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-medium text-info-800">Menunggu Verifikasi</h3>
                                <div class="mt-2 text-sm text-info-700">
                                    <p>Bukti pembayaran Anda sudah diterima. Tim kami sedang memverifikasi pembayaran dan saldo akan masuk otomatis setelah verifikasi selesai (maks. 1x24 jam).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif ($transaction->isWithdrawal())
                <!-- Info penarikan -->
                <div class="bg-warning-50 border border-warning-200 rounded-xl p-5">
                    <div class="flex">
                        <div class="flex-shrink-0 pt-0.5">
                            <i class="fas fa-hourglass-half text-warning-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-base font-medium text-warning-800">Proses Penarikan</h3>
                            <div class="mt-2 text-sm text-warning-700">
                                <ul class="list-disc list-inside space-y-1.5">
                                    <li>Permintaan penarikan sedang diproses</li>
                                    <li>Dana akan ditransfer ke rekening Anda dalam 1-3 hari kerja</li>
                                    <li>Anda akan mendapat notifikasi saat transfer selesai</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @endif

        <!-- Important Info -->
        <div class="mt-8 bg-neutral-50 border border-neutral-200 rounded-xl p-5">
            <div class="flex">
                <div class="flex-shrink-0 pt-0.5">
                    <i class="fas fa-exclamation-circle text-neutral-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-base font-medium text-neutral-800">Informasi Penting</h3>
                    <div class="mt-2 text-sm text-neutral-700">
                        <ul class="list-disc list-inside space-y-1.5">
                            <li>Simpan screenshot halaman ini sebagai bukti transaksi</li>
                            <li>Jika mengalami kendala, hubungi customer service</li>
                            <li>Transaksi yang sudah berhasil tidak dapat dibatalkan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.plain-app>