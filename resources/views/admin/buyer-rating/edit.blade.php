<x-layouts.plain-app>
    <div class="min-h-screen bg-neutral-50 py-8">
        <div class=" mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header with Gradient Background --}}
            <div class="mb-8 bg-gradient-to-r from-primary-50 to-secondary-50 rounded-xl p-6 border border-primary-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-neutral-800">Edit Buyer Rating</h1>
                        <p class="mt-1 text-sm text-neutral-600">Edit rating untuk buyer: <span
                                class="font-medium">{{ $rating->name }}</span></p>
                    </div>
                    <a href="{{ route('admin.buyer-ratings.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-neutral-600 text-white text-sm font-medium rounded-lg hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Current Status Card --}}
            <div class="mb-6 bg-white rounded-xl shadow-xs border border-neutral-200 p-6">
                <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-primary-600 mr-2"></i>
                    Status Saat Ini
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-secondary-50 rounded-xl p-4 border border-secondary-100">
                        <h4 class="text-sm font-medium text-neutral-600">Total Orders</h4>
                        <p class="text-2xl font-bold text-secondary-600">{{ $rating->total_orders }}</p>
                    </div>
                    <div class="bg-success-50 rounded-xl p-4 border border-success-100">
                        <h4 class="text-sm font-medium text-neutral-600">Orders Sukses</h4>
                        <p class="text-2xl font-bold text-success-600">{{ $rating->successful_orders }}</p>
                    </div>
                    <div class="bg-primary-50 rounded-xl p-4 border border-primary-100">
                        <h4 class="text-sm font-medium text-neutral-600">Success Rate</h4>
                        <p class="text-2xl font-bold text-primary-600">{{ number_format($rating->success_rate, 1) }}%
                        </p>
                    </div>
                    <div
                        class="bg-{{ $rating->risk_level === 'low' ? 'success' : ($rating->risk_level === 'medium' ? 'warning' : 'error') }}-50 rounded-xl p-4 border border-{{ $rating->risk_level === 'low' ? 'success' : ($rating->risk_level === 'medium' ? 'warning' : 'error') }}-100">
                        <h4 class="text-sm font-medium text-neutral-600">Risk Level</h4>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            bg-{{ $rating->risk_level === 'low' ? 'success' : ($rating->risk_level === 'medium' ? 'warning' : 'error') }}-100 
                            text-{{ $rating->risk_level === 'low' ? 'success' : ($rating->risk_level === 'medium' ? 'warning' : 'error') }}-800">
                            {{ ucfirst($rating->risk_level->value) }} Risk
                        </span>
                    </div>
                </div>
            </div>

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="mb-6 bg-error-50 border border-error-200 rounded-xl p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-error-600 mr-2 mt-0.5"></i>
                        <div>
                            <h3 class="text-sm font-medium text-error-800">Terdapat beberapa kesalahan:</h3>
                            <ul class="mt-2 text-sm text-error-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <div class="bg-white rounded-xl shadow-xs border border-neutral-200">
                <form method="POST" action="{{ route('admin.buyer-ratings.update', $rating->id) }}"
                    class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Buyer Information --}}
                    <div class="border-b border-neutral-200 pb-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                            <i class="fas fa-user-tag text-primary-600 mr-2"></i>
                            Informasi Buyer
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-neutral-700">
                                    Nomor Telepon <span class="text-error-500">*</span>
                                </label>
                                <input type="text" id="phone_number" name="phone_number"
                                    value="{{ old('phone_number', $rating->phone_number) }}" placeholder="08xxxxxxxxxx"
                                    readonly
                                    class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-lg shadow-sm bg-neutral-50 text-neutral-500 cursor-not-allowed">
                                <p class="mt-1 text-sm text-neutral-500">Nomor telepon tidak dapat diubah</p>
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-neutral-700">
                                    Nama Buyer <span class="text-error-500">*</span>
                                </label>
                                <input type="text" id="name" name="name"
                                    value="{{ old('name', $rating->name) }}" placeholder="Nama lengkap buyer"
                                    class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-error-300 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Order Statistics --}}
                    <div class="border-b border-neutral-200 pb-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                            <i class="fas fa-chart-pie text-primary-600 mr-2"></i>
                            Statistik Orders
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label for="total_orders" class="block text-sm font-medium text-neutral-700">
                                    Total Orders <span class="text-error-500">*</span>
                                </label>
                                <input type="number" id="total_orders" name="total_orders"
                                    value="{{ old('total_orders', $rating->total_orders) }}" min="0"
                                    class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('total_orders') border-error-300 @enderror">
                                @error('total_orders')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="successful_orders" class="block text-sm font-medium text-neutral-700">
                                    Orders Sukses <span class="text-error-500">*</span>
                                </label>
                                <input type="number" id="successful_orders" name="successful_orders"
                                    value="{{ old('successful_orders', $rating->successful_orders) }}" min="0"
                                    class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('successful_orders') border-error-300 @enderror">
                                @error('successful_orders')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="failed_cod_orders" class="block text-sm font-medium text-neutral-700">
                                    COD Gagal
                                </label>
                                <input type="number" id="failed_cod_orders" name="failed_cod_orders"
                                    value="{{ old('failed_cod_orders', $rating->failed_cod_orders) }}" min="0"
                                    class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('failed_cod_orders') border-error-300 @enderror">
                                @error('failed_cod_orders')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cancelled_orders" class="block text-sm font-medium text-neutral-700">
                                    Orders Dibatalkan
                                </label>
                                <input type="number" id="cancelled_orders" name="cancelled_orders"
                                    value="{{ old('cancelled_orders', $rating->cancelled_orders) }}" min="0"
                                    class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('cancelled_orders') border-error-300 @enderror">
                                @error('cancelled_orders')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-primary-50 rounded-xl border border-primary-100">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-primary-600 mr-2 mt-0.5"></i>
                                <div class="text-sm text-primary-700">
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
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                            <i class="fas fa-sticky-note text-primary-600 mr-2"></i>
                            Catatan Tambahan
                        </h3>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-neutral-700">
                                Notes (Opsional)
                            </label>
                            <textarea id="notes" name="notes" rows="4" placeholder="Tambahkan catatan khusus tentang buyer ini..."
                                class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('notes') border-error-300 @enderror">{{ old('notes', $rating->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-neutral-200">
                        <a href="{{ route('admin.buyer-ratings.index') }}"
                            class="px-6 py-2 border border-neutral-300 text-neutral-700 text-sm font-medium rounded-lg hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-check-circle mr-2"></i>
                            Update Buyer Rating
                        </button>
                    </div>
                </form>
            </div>

            {{-- Preview Card --}}
            <div class="mt-8 bg-white rounded-xl shadow-xs border border-neutral-200 p-6">
                <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                    <i class="fas fa-eye text-primary-600 mr-2"></i>
                    Preview Perubahan
                </h3>
                <div id="preview-calculation" class="text-sm text-neutral-600">
                    <p>Data akan diperbarui berdasarkan input yang dimasukkan.</p>
                </div>
            </div>

            {{-- History Log --}}
            <div class="mt-8 bg-white rounded-xl shadow-xs border border-neutral-200 p-6">
                <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                    <i class="fas fa-history text-primary-600 mr-2"></i>
                    Informasi Terakhir Diupdate
                </h3>
                <div class="text-sm text-neutral-600 space-y-2">
                    <div class="bg-neutral-50 p-3 rounded-lg">
                        <p><strong>Dibuat:</strong> {{ $rating->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="bg-neutral-50 p-3 rounded-lg">
                        <p><strong>Terakhir Diupdate:</strong> {{ $rating->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if ($rating->created_at->ne($rating->updated_at))
                        <div class="bg-primary-50 p-3 rounded-lg border border-primary-100">
                            <p class="text-primary-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Perubahan Terdeteksi:</strong> Data ini sudah pernah diupdate sebelumnya
                            </p>
                        </div>
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
                    previewDiv.innerHTML =
                        '<div class="bg-error-50 p-3 rounded-lg border border-error-100"><p class="text-error-700"><i class="fas fa-exclamation-circle mr-1"></i>Total Orders tidak boleh 0 untuk menghitung success rate.</p></div>';
                    return;
                }

                if (successfulOrders > totalOrders) {
                    previewDiv.innerHTML =
                        '<div class="bg-error-50 p-3 rounded-lg border border-error-100"><p class="text-error-700"><i class="fas fa-exclamation-circle mr-1"></i>Orders Sukses tidak boleh lebih besar dari Total Orders.</p></div>';
                    return;
                }

                const newSuccessRate = (successfulOrders / totalOrders) * 100;
                let newRiskLevel = 'low';
                let riskColor = 'text-success-600';
                let riskBg = 'bg-success-100';

                if (newSuccessRate < 60) {
                    newRiskLevel = 'high';
                    riskColor = 'text-error-600';
                    riskBg = 'bg-error-100';
                } else if (newSuccessRate < 80) {
                    newRiskLevel = 'medium';
                    riskColor = 'text-warning-600';
                    riskBg = 'bg-warning-100';
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
                        const changeColor = successRateChange > 0 ? 'text-success-600' : 'text-error-600';
                        const changeIcon = successRateChange > 0 ? '↑' : '↓';
                        changeIndicator =
                            `<span class="${changeColor} font-medium">${changeIcon} ${Math.abs(successRateChange).toFixed(1)}%</span>`;
                    }
                }

                previewDiv.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-neutral-50 rounded-xl p-4 border border-neutral-200">
                            <h4 class="font-medium text-neutral-800">Order Summary Baru</h4>
                            <p class="text-sm mt-1">Total Orders: <span class="font-medium ${totalOrders !== currentData.totalOrders ? 'text-primary-600' : ''}">${totalOrders}</span></p>
                            <p class="text-sm text-success-600">Sukses: <span class="${successfulOrders !== currentData.successfulOrders ? 'font-bold' : ''}">${successfulOrders}</span></p>
                            <p class="text-sm text-error-600">Gagal: <span class="${failedOrders !== (currentData.failedCodOrders + currentData.cancelledOrders) ? 'font-bold' : ''}">${failedOrders}</span></p>
                        </div>
                        <div class="bg-neutral-50 rounded-xl p-4 border border-neutral-200">
                            <h4 class="font-medium text-neutral-800">Success Rate Baru</h4>
                            <p class="text-2xl font-bold ${Math.abs(successRateChange) > 0.1 ? (successRateChange > 0 ? 'text-success-600' : 'text-error-600') : 'text-neutral-800'}">${newSuccessRate.toFixed(1)}%</p>
                            ${changeIndicator ? `<p class="text-xs mt-1">Perubahan: ${changeIndicator}</p>` : ''}
                            <div class="w-full bg-neutral-200 rounded-full h-2 mt-2">
                                <div class="bg-${newSuccessRate >= 80 ? 'success' : (newSuccessRate >= 60 ? 'warning' : 'error')}-500 h-2 rounded-full transition-all duration-300" style="width: ${newSuccessRate}%"></div>
                            </div>
                        </div>
                        <div class="bg-neutral-50 rounded-xl p-4 border border-neutral-200">
                            <h4 class="font-medium text-neutral-800">Risk Level Baru</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${riskBg} ${riskColor} ${riskLevelChanged ? 'ring-2 ring-primary-400' : ''}">
                                ${newRiskLevel.charAt(0).toUpperCase() + newRiskLevel.slice(1)} Risk
                            </span>
                            ${riskLevelChanged ? `<p class="text-xs text-primary-600 mt-1">Risk level akan berubah!</p>` : ''}
                        </div>
                    </div>
                    ${hasChanges ? `
                                <div class="mt-4 p-3 bg-primary-50 border border-primary-200 rounded-lg">
                                    <p class="text-sm text-primary-800">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <strong>Perubahan Terdeteksi:</strong> Data akan diupdate setelah form disimpan.
                                    </p>
                                </div>
                            ` : `
                                <div class="mt-4 p-3 bg-neutral-50 border border-neutral-200 rounded-lg">
                                    <p class="text-sm text-neutral-600">Tidak ada perubahan dari data saat ini.</p>
                                </div>
                            `}
                `;
            }

            // Validasi real-time
            function validateInputs() {
                const totalOrders = parseInt(totalOrdersInput.value) || 0;
                const successfulOrders = parseInt(successfulOrdersInput.value) || 0;

                // Reset border colors
                successfulOrdersInput.classList.remove('border-error-300');

                if (successfulOrders > totalOrders && totalOrders > 0) {
                    successfulOrdersInput.classList.add('border-error-300');
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
