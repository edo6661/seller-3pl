<x-layouts.plain-app>
    <x-slot name="title">Buat Tiket Bantuan</x-slot>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8 p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl text-white">
            <h1 class="text-2xl font-bold mb-2 flex items-center">
                <i class="fas fa-life-ring mr-3"></i>
                Buat Tiket Bantuan
            </h1>
            <p class="opacity-90">Sampaikan masalah atau keluhan Anda kepada tim support</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg border border-neutral-100">
            <form action="{{ route('seller.support.store') }}" method="POST" enctype="multipart/form-data" id="ticketForm">
                @csrf
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">
                            <i class="fas fa-tag text-primary-500 mr-1"></i>
                            Tipe Tiket
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input type="radio" id="general" name="ticket_type" value="general" 
                                    {{ old('ticket_type', 'general') === 'general' ? 'checked' : '' }}
                                    class="hidden peer">
                                <label for="general" 
                                    class="flex items-center p-4 border-2 border-neutral-200 rounded-lg cursor-pointer
                                           peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:bg-neutral-50 transition">
                                    <div class="mr-3 p-2 bg-orange-100 rounded-lg text-orange-600">
                                        <i class="fas fa-question-circle"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-neutral-900">Masalah Umum</div>
                                        <div class="text-sm text-neutral-600">Masalah aplikasi, akun, atau pertanyaan umum</div>
                                    </div>
                                </label>
                            </div>
                            <div>
                                <input type="radio" id="shipment" name="ticket_type" value="shipment"
                                    {{ old('ticket_type') === 'shipment' ? 'checked' : '' }}
                                    class="hidden peer">
                                <label for="shipment" 
                                    class="flex items-center p-4 border-2 border-neutral-200 rounded-lg cursor-pointer
                                           peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:bg-neutral-50 transition">
                                    <div class="mr-3 p-2 bg-blue-100 rounded-lg text-blue-600">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-neutral-900">Masalah Pengiriman</div>
                                        <div class="text-sm text-neutral-600">Masalah terkait pickup request atau pengiriman</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @error('ticket_type')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div id="shipmentInfo" class="hidden">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="font-medium text-blue-900 mb-3 flex items-center">
                                <i class="fas fa-search mr-2"></i>
                                Informasi Pengiriman
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-1">
                                        Kode Pickup <span class="text-error-500">*</span>
                                    </label>
                                    <div class="flex">
                                        <input type="text" name="pickup_code" id="pickupCode" 
                                            value="{{ old('pickup_code') }}"
                                            placeholder="Contoh: PU240101001"
                                            class="flex-1 border border-neutral-300 rounded-l-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <button type="button" id="searchPickup" 
                                            class="px-4 py-2 bg-primary-600 text-white rounded-r-lg hover:bg-primary-700 transition">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-1">Nomor Resi</label>
                                    <input type="text" name="tracking_number" id="trackingNumber"
                                        value="{{ old('tracking_number') }}"
                                        placeholder="Nomor resi (opsional)"
                                        class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                            </div>
                            <div id="pickupInfo" class="hidden mt-4 p-3 bg-white rounded-lg border border-blue-300">
                                <h5 class="font-medium text-blue-900 mb-2">Detail Pickup Request</h5>
                                <div id="pickupDetails" class="text-sm text-blue-700"></div>
                            </div>
                        </div>
                        @error('pickup_code')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">
                            <i class="fas fa-list text-primary-500 mr-1"></i>
                            Kategori Masalah <span class="text-error-500">*</span>
                        </label>
                        <select name="category" class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="delivery_issue" {{ old('category') === 'delivery_issue' ? 'selected' : '' }}>Masalah Pengiriman</option>
                            <option value="payment_issue" {{ old('category') === 'payment_issue' ? 'selected' : '' }}>Masalah Pembayaran</option>
                            <option value="item_damage" {{ old('category') === 'item_damage' ? 'selected' : '' }}>Barang Rusak</option>
                            <option value="item_lost" {{ old('category') === 'item_lost' ? 'selected' : '' }}>Barang Hilang</option>
                            <option value="wrong_address" {{ old('category') === 'wrong_address' ? 'selected' : '' }}>Alamat Salah</option>
                            <option value="courier_service" {{ old('category') === 'courier_service' ? 'selected' : '' }}>Masalah Kurir</option>
                            <option value="app_technical" {{ old('category') === 'app_technical' ? 'selected' : '' }}>Masalah Teknis Aplikasi</option>
                            <option value="account_issue" {{ old('category') === 'account_issue' ? 'selected' : '' }}>Masalah Akun</option>
                            <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">
                            <i class="fas fa-exclamation-triangle text-primary-500 mr-1"></i>
                            Prioritas <span class="text-error-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <input type="radio" id="low" name="priority" value="low" 
                                    {{ old('priority', 'medium') === 'low' ? 'checked' : '' }}
                                    class="hidden peer">
                                <label for="low" 
                                    class="block p-3 text-center border-2 border-neutral-200 rounded-lg cursor-pointer
                                           peer-checked:border-gray-500 peer-checked:bg-gray-50 hover:bg-neutral-50 transition">
                                    <div class="text-gray-600 mb-1"><i class="fas fa-minus-circle"></i></div>
                                    <div class="text-sm font-medium">Rendah</div>
                                </label>
                            </div>
                            <div>
                                <input type="radio" id="medium" name="priority" value="medium"
                                    {{ old('priority', 'medium') === 'medium' ? 'checked' : '' }}
                                    class="hidden peer">
                                <label for="medium" 
                                    class="block p-3 text-center border-2 border-neutral-200 rounded-lg cursor-pointer
                                           peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-neutral-50 transition">
                                    <div class="text-blue-600 mb-1"><i class="fas fa-circle"></i></div>
                                    <div class="text-sm font-medium">Sedang</div>
                                </label>
                            </div>
                            <div>
                                <input type="radio" id="high" name="priority" value="high"
                                    {{ old('priority') === 'high' ? 'checked' : '' }}
                                    class="hidden peer">
                                <label for="high" 
                                    class="block p-3 text-center border-2 border-neutral-200 rounded-lg cursor-pointer
                                           peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-neutral-50 transition">
                                    <div class="text-orange-600 mb-1"><i class="fas fa-exclamation-circle"></i></div>
                                    <div class="text-sm font-medium">Tinggi</div>
                                </label>
                            </div>
                            <div>
                                <input type="radio" id="urgent" name="priority" value="urgent"
                                    {{ old('priority') === 'urgent' ? 'checked' : '' }}
                                    class="hidden peer">
                                <label for="urgent" 
                                    class="block p-3 text-center border-2 border-neutral-200 rounded-lg cursor-pointer
                                           peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-neutral-50 transition">
                                    <div class="text-red-600 mb-1"><i class="fas fa-exclamation-triangle"></i></div>
                                    <div class="text-sm font-medium">Mendesak</div>
                                </label>
                            </div>
                        </div>
                        @error('priority')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-neutral-700 mb-2">
                            <i class="fas fa-heading text-primary-500 mr-1"></i>
                            Subjek <span class="text-error-500">*</span>
                        </label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                            placeholder="Ringkasan singkat masalah Anda"
                            class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        @error('subject')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-neutral-700 mb-2">
                            <i class="fas fa-align-left text-primary-500 mr-1"></i>
                            Deskripsi Masalah <span class="text-error-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="6"
                            placeholder="Jelaskan masalah Anda secara detail..."
                            class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">{{ old('description') }}</textarea>
                        <div class="mt-1 text-xs text-neutral-500">Maksimal 2000 karakter</div>
                        @error('description')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">
                            <i class="fas fa-paperclip text-primary-500 mr-1"></i>
                            Lampiran (Opsional)
                        </label>
                        <div class="border-2 border-dashed border-neutral-300 rounded-lg p-6 text-center">
                            <input type="file" id="attachments" name="attachments[]" multiple
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                class="hidden" onchange="handleFileSelect(this)">
                            <label for="attachments" class="cursor-pointer">
                                <div class="text-neutral-400 text-3xl mb-2">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="text-sm text-neutral-600">
                                    Klik untuk memilih file atau drag & drop
                                </div>
                                <div class="text-xs text-neutral-500 mt-1">
                                    Format: JPG, PNG, PDF, DOC, DOCX (Max 2MB, 5 file)
                                </div>
                            </label>
                        </div>
                        <div id="fileList" class="mt-3 hidden">
                            <div class="text-sm font-medium text-neutral-700 mb-2">File terpilih:</div>
                            <div id="fileItems" class="space-y-2"></div>
                        </div>
                        @error('attachments')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                        @error('attachments.*')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="px-6 py-4 bg-neutral-50 border-t border-neutral-200 rounded-b-xl">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('seller.support.index') }}" 
                            class="px-4 py-2 text-neutral-600 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                        <button type="submit" 
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-md flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Tiket
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ticketTypeInputs = document.querySelectorAll('input[name="ticket_type"]');
            const shipmentInfo = document.getElementById('shipmentInfo');
            const searchButton = document.getElementById('searchPickup');
            const pickupCodeInput = document.getElementById('pickupCode');
            const trackingNumberInput = document.getElementById('trackingNumber');
            const pickupInfo = document.getElementById('pickupInfo');
            const pickupDetails = document.getElementById('pickupDetails');
            @if(isset($ticketData))
                document.getElementById('shipment').checked = true;
                shipmentInfo.classList.remove('hidden');
                pickupCodeInput.value = '{{ $ticketData['pickup_code'] }}';
                @if(!empty($ticketData['tracking_number']))
                    trackingNumberInput.value = '{{ $ticketData['tracking_number'] }}';
                @endif
                pickupDetails.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div><strong>Kode:</strong> {{ $ticketData['pickup_code'] }}</div>
                        <div><strong>Status:</strong> <span class="capitalize">{{ $ticketData['pickup_request']->status }}</span></div>
                        <div><strong>Penerima:</strong> {{ $ticketData['pickup_request']->recipient_name }}</div>
                        <div><strong>Total:</strong> Rp {{ number_format($ticketData['pickup_request']->total_amount, 0, ',', '.') }}</div>
                        <div><strong>Tanggal:</strong> {{ $ticketData['pickup_request']->created_at->format('d M Y') }}</div>
                        @if($ticketData['pickup_request']->courier_tracking_number)
                        <div><strong>Resi:</strong> {{ $ticketData['pickup_request']->courier_tracking_number }}</div>
                        @endif
                    </div>
                `;
                pickupInfo.classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('subject').focus();
                }, 100);
            @endif
            ticketTypeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value === 'shipment') {
                        shipmentInfo.classList.remove('hidden');
                    } else {
                        shipmentInfo.classList.add('hidden');
                        pickupInfo.classList.add('hidden');
                    }
                });
            });
            searchButton.addEventListener('click', function() {
                const pickupCode = pickupCodeInput.value.trim();
                if (!pickupCode) {
                    alert('Masukkan kode pickup terlebih dahulu');
                    return;
                }
                searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                searchButton.disabled = true;
                fetch(`{{ route('seller.support.search-pickup') }}?identifier=${pickupCode}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            pickupDetails.innerHTML = `
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div><strong>Kode:</strong> ${data.data.pickup_code}</div>
                                    <div><strong>Status:</strong> <span class="capitalize">${data.data.status}</span></div>
                                    <div><strong>Penerima:</strong> ${data.data.recipient_name}</div>
                                    <div><strong>Total:</strong> Rp ${new Intl.NumberFormat('id-ID').format(data.data.total_amount)}</div>
                                    <div><strong>Tanggal:</strong> ${data.data.created_at}</div>
                                    ${data.data.tracking_number ? `<div><strong>Resi:</strong> ${data.data.tracking_number}</div>` : ''}
                                </div>
                            `;
                            pickupInfo.classList.remove('hidden');
                            if (data.data.tracking_number) {
                                trackingNumberInput.value = data.data.tracking_number;
                            }
                        } else {
                            alert(data.message);
                            pickupInfo.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mencari data');
                    })
                    .finally(() => {
                        searchButton.innerHTML = '<i class="fas fa-search"></i>';
                        searchButton.disabled = false;
                    });
            });
            window.handleFileSelect = function(input) {
                const fileList = document.getElementById('fileList');
                const fileItems = document.getElementById('fileItems');
                if (input.files.length > 0) {
                    fileList.classList.remove('hidden');
                    fileItems.innerHTML = '';
                    Array.from(input.files).forEach((file, index) => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'flex items-center justify-between p-2 bg-neutral-50 rounded border';
                        fileItem.innerHTML = `
                            <div class="flex items-center">
                                <i class="fas fa-file text-neutral-400 mr-2"></i>
                                <span class="text-sm">${file.name}</span>
                                <span class="text-xs text-neutral-500 ml-2">(${(file.size / 1024).toFixed(1)} KB)</span>
                            </div>
                            <button type="button" onclick="removeFile(${index})" class="text-error-500 hover:text-error-700">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        fileItems.appendChild(fileItem);
                    });
                } else {
                    fileList.classList.add('hidden');
                }
            };
            window.removeFile = function(index) {
                const input = document.getElementById('attachments');
                const dt = new DataTransfer();
                const files = Array.from(input.files);
                files.forEach((file, i) => {
                    if (i !== index) {
                        dt.items.add(file);
                    }
                });
                input.files = dt.files;
                handleFileSelect(input);
            };
        });
        </script>
</x-layouts.plain-app>