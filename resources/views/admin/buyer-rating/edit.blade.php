{{-- resources/views/admin/buyer-rating/edit.blade.php --}}

<x-layouts.plain-app>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Buyer Rating</h1>
                        <p class="mt-2 text-gray-600">Edit rating untuk buyer: <span class="font-medium">{{ $rating->name }}</span></p>
                    </div>
                    <a href="{{ route('admin.buyer-ratings.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Current Status Card --}}
            <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status Saat Ini</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-700">Total Orders</h4>
                        <p class="text-2xl font-bold text-gray-900">{{ $rating->total_orders }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-700">Orders Sukses</h4>
                        <p class="text-2xl font-bold text-green-600">{{ $rating->successful_orders }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-700">Success Rate</h4>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($rating->success_rate, 1) }}%</p>
                    </div>
                    <div class="bg-{{ $rating->risk_level === 'low' ? 'green' : ($rating->risk_level === 'medium' ? 'yellow' : 'red') }}-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-700">Risk Level</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            bg-{{ $rating->risk_level === 'low' ? 'green' : ($rating->risk_level === 'medium' ? 'yellow' : 'red') }}-100 
                            text-{{ $rating->risk_level === 'low' ? 'green' : ($rating->risk_level === 'medium' ? 'yellow' : 'red') }}-800">
                            {{ ucfirst($rating->risk_level->value) }} Risk
                        </span>
                    </div>
                </div>
            </div>

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form method="POST" action="{{ route('admin.buyer-ratings.update', $rating->id) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Buyer Information --}}
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Buyer</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="phone_number" 
                                       name="phone_number" 
                                       value="{{ old('phone_number', $rating->phone_number) }}"
                                       placeholder="08xxxxxxxxxx"
                                       readonly
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                                <p class="mt-1 text-sm text-gray-500">Nomor telepon tidak dapat diubah</p>
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Nama Buyer <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $rating->name) }}"
                                       placeholder="Nama lengkap buyer"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Order Statistics --}}
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Orders</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label for="total_orders" class="block text-sm font-medium text-gray-700">
                                    Total Orders <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       id="total_orders" 
                                       name="total_orders" 
                                       value="{{ old('total_orders', $rating->total_orders) }}"
                                       min="0"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('total_orders') border-red-300 @enderror">
                                @error('total_orders')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="successful_orders" class="block text-sm font-medium text-gray-700">
                                    Orders Sukses <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       id="successful_orders" 
                                       name="successful_orders" 
                                       value="{{ old('successful_orders', $rating->successful_orders) }}"
                                       min="0"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('successful_orders') border-red-300 @enderror">
                                @error('successful_orders')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="failed_cod_orders" class="block text-sm font-medium text-gray-700">
                                    COD Gagal
                                </label>
                                <input type="number" 
                                       id="failed_cod_orders" 
                                       name="failed_cod_orders" 
                                       value="{{ old('failed_cod_orders', $rating->failed_cod_orders) }}"
                                       min="0"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('failed_cod_orders') border-red-300 @enderror">
                                @error('failed_cod_orders')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cancelled_orders" class="block text-sm font-medium text-gray-700">
                                    Orders Dibatalkan
                                </label>
                                <input type="number" 
                                       id="cancelled_orders" 
                                       name="cancelled_orders" 
                                       value="{{ old('cancelled_orders', $rating->cancelled_orders) }}"
                                       min="0"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('cancelled_orders') border-red-300 @enderror">
                                @error('cancelled_orders')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-blue-700">
                                    <p><strong>Catatan:</strong></p>
                                    <p>• Success Rate akan dihitung ulang otomatis berdasarkan perubahan data orders</p>
                                    <p>• Risk Level akan diperbarui otomatis berdasarkan Success Rate yang baru</p>
                                    <p>• Pastikan jumlah "Orders Sukses" tidak melebihi "Total Orders"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Notes --}}
                    <div class="pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan Tambahan</h3>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Notes (Opsional)
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="4" 
                                      placeholder="Tambahkan catatan khusus tentang buyer ini..."
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror">{{ old('notes', $rating->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.buyer-ratings.index') }}" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Buyer Rating
                        </button>
                    </div>
                </form>
            </div>

            {{-- Preview Card --}}
            <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Preview Perubahan</h3>
                <div id="preview-calculation" class="text-sm text-gray-600">
                    <p>Data akan diperbarui berdasarkan input yang dimasukkan.</p>
                </div>
            </div>

            {{-- History Log (Optional) --}}
            <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Terakhir Diupdate</h3>
                <div class="text-sm text-gray-600">
                    <p><strong>Dibuat:</strong> {{ $rating->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Terakhir Diupdate:</strong> {{ $rating->updated_at->format('d/m/Y H:i') }}</p>
                    @if($rating->created_at->ne($rating->updated_at))
                        <p class="text-blue-600 mt-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Data ini sudah pernah diupdate sebelumnya
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript untuk Real-time Calculation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalOrdersInput = document.getElementById('total_orders');
            const successfulOrdersInput = document.getElementById('successful_orders');
            const failedCodOrdersInput = document.getElementById('failed_cod_orders');
            const cancelledOrdersInput = document.getElementById('cancelled_orders');
            const previewDiv = document.getElementById('preview-calculation');

            // Data saat ini untuk perbandingan
            const currentData = {
                totalOrders: {{ $rating->total_orders }},
                successfulOrders: {{ $rating->successful_orders }},
                failedCodOrders: {{ $rating->failed_cod_orders }},
                cancelledOrders: {{ $rating->cancelled_orders }},
                successRate: {{ $rating->success_rate }},
                riskLevel: '{{ $rating->risk_level }}'
            };

            function updatePreview() {
                const totalOrders = parseInt(totalOrdersInput.value) || 0;
                const successfulOrders = parseInt(successfulOrdersInput.value) || 0;
                const failedCodOrders = parseInt(failedCodOrdersInput.value) || 0;
                const cancelledOrders = parseInt(cancelledOrdersInput.value) || 0;

                if (totalOrders === 0) {
                    previewDiv.innerHTML = '<p class="text-red-600">Total Orders tidak boleh 0 untuk menghitung success rate.</p>';
                    return;
                }

                if (successfulOrders > totalOrders) {
                    previewDiv.innerHTML = '<p class="text-red-600">Orders Sukses tidak boleh lebih besar dari Total Orders.</p>';
                    return;
                }

                const newSuccessRate = (successfulOrders / totalOrders) * 100;
                let newRiskLevel = 'low';
                let riskColor = 'text-green-600';
                let riskBg = 'bg-green-100';

                if (newSuccessRate < 60) {
                    newRiskLevel = 'high';
                    riskColor = 'text-red-600';
                    riskBg = 'bg-red-100';
                } else if (newSuccessRate < 80) {
                    newRiskLevel = 'medium';
                    riskColor = 'text-yellow-600';
                    riskBg = 'bg-yellow-100';
                }

                const failedOrders = failedCodOrders + cancelledOrders;

                // Deteksi perubahan
                const hasChanges = 
                    totalOrders !== currentData.totalOrders ||
                    successfulOrders !== currentData.successfulOrders ||
                    failedCodOrders !== currentData.failedCodOrders ||
                    cancelledOrders !== currentData.cancelledOrders;

                const successRateChange = newSuccessRate - currentData.successRate;
                const riskLevelChanged = newRiskLevel !== currentData.riskLevel;

                let changeIndicator = '';
                if (hasChanges) {
                    if (Math.abs(successRateChange) > 0.1) {
                        const changeColor = successRateChange > 0 ? 'text-green-600' : 'text-red-600';
                        const changeIcon = successRateChange > 0 ? '↑' : '↓';
                        changeIndicator = `<span class="${changeColor} font-medium">${changeIcon} ${Math.abs(successRateChange).toFixed(1)}%</span>`;
                    }
                }

                previewDiv.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">Order Summary Baru</h4>
                            <p class="text-sm mt-1">Total Orders: <span class="font-medium ${totalOrders !== currentData.totalOrders ? 'text-blue-600' : ''}">${totalOrders}</span></p>
                            <p class="text-sm text-green-600">Sukses: <span class="${successfulOrders !== currentData.successfulOrders ? 'font-bold' : ''}">${successfulOrders}</span></p>
                            <p class="text-sm text-red-600">Gagal: <span class="${failedOrders !== (currentData.failedCodOrders + currentData.cancelledOrders) ? 'font-bold' : ''}">${failedOrders}</span></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">Success Rate Baru</h4>
                            <p class="text-2xl font-bold ${Math.abs(successRateChange) > 0.1 ? (successRateChange > 0 ? 'text-green-600' : 'text-red-600') : 'text-gray-900'}">${newSuccessRate.toFixed(1)}%</p>
                            ${changeIndicator ? `<p class="text-xs mt-1">Perubahan: ${changeIndicator}</p>` : ''}
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-${newSuccessRate >= 80 ? 'green' : (newSuccessRate >= 60 ? 'yellow' : 'red')}-500 h-2 rounded-full transition-all duration-300" style="width: ${newSuccessRate}%"></div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">Risk Level Baru</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${riskBg} ${riskColor} ${riskLevelChanged ? 'ring-2 ring-blue-400' : ''}">
                                ${newRiskLevel.charAt(0).toUpperCase() + newRiskLevel.slice(1)} Risk
                            </span>
                            ${riskLevelChanged ? `<p class="text-xs text-blue-600 mt-1">Risk level akan berubah!</p>` : ''}
                        </div>
                    </div>
                    ${hasChanges ? `
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <strong>Perubahan Terdeteksi:</strong> Data akan diupdate setelah form disimpan.
                            </p>
                        </div>
                    ` : `
                        <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-sm text-gray-600">Tidak ada perubahan dari data saat ini.</p>
                        </div>
                    `}
                `;
            }

            // Validasi real-time
            function validateInputs() {
                const totalOrders = parseInt(totalOrdersInput.value) || 0;
                const successfulOrders = parseInt(successfulOrdersInput.value) || 0;

                // Reset border colors
                successfulOrdersInput.classList.remove('border-red-300');
                
                if (successfulOrders > totalOrders && totalOrders > 0) {
                    successfulOrdersInput.classList.add('border-red-300');
                }
            }

            // Add event listeners
            [totalOrdersInput, successfulOrdersInput, failedCodOrdersInput, cancelledOrdersInput].forEach(input => {
                input.addEventListener('input', function() {
                    updatePreview();
                    validateInputs();
                });
            });

            // Initial preview
            updatePreview();
        });
    </script>
</x-layouts.plain-app>