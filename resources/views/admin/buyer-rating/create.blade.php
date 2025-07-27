{{-- resources/views/admin/buyer-rating/create.blade.php --}}
<x-layouts.plain-app>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Tambah Buyer Rating</h1>
                        <p class="mt-2 text-gray-600">Tambahkan rating baru untuk buyer</p>
                    </div>
                    <a href="{{ route('admin.buyer-ratings.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form method="POST" action="{{ route('admin.buyer-ratings.store') }}" class="p-6 space-y-6">
                    @csrf

                    {{-- Hidden fields for calculated values --}}
                    <input type="hidden" id="success_rate" name="success_rate" value="{{ old('success_rate', 0) }}">
                    <input type="hidden" id="risk_level" name="risk_level" value="{{ old('risk_level', 'low') }}">

                    {{-- Buyer Information --}}
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Buyer</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="phone_number" name="phone_number"
                                    value="{{ old('phone_number') }}" placeholder="08xxxxxxxxxx"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone_number') border-red-300 @enderror">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Nama Buyer <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
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
                                <input type="number" id="total_orders" name="total_orders"
                                    value="{{ old('total_orders', 0) }}" min="0"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('total_orders') border-red-300 @enderror">
                                @error('total_orders')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="successful_orders" class="block text-sm font-medium text-gray-700">
                                    Orders Sukses <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="successful_orders" name="successful_orders"
                                    value="{{ old('successful_orders', 0) }}" min="0"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('successful_orders') border-red-300 @enderror">
                                @error('successful_orders')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="failed_cod_orders" class="block text-sm font-medium text-gray-700">
                                    COD Gagal
                                </label>
                                <input type="number" id="failed_cod_orders" name="failed_cod_orders"
                                    value="{{ old('failed_cod_orders', 0) }}" min="0"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('failed_cod_orders') border-red-300 @enderror">
                                @error('failed_cod_orders')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cancelled_orders" class="block text-sm font-medium text-gray-700">
                                    Orders Dibatalkan
                                </label>
                                <input type="number" id="cancelled_orders" name="cancelled_orders"
                                    value="{{ old('cancelled_orders', 0) }}" min="0"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('cancelled_orders') border-red-300 @enderror">
                                @error('cancelled_orders')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-blue-700">
                                    <p><strong>Catatan:</strong></p>
                                    <p>• Success Rate akan dihitung otomatis berdasarkan Total Orders dan Orders Sukses
                                    </p>
                                    <p>• Risk Level akan ditentukan otomatis berdasarkan Success Rate (≥80% = Low Risk,
                                        60-79% = Medium Risk, <60%=High Risk)</p>
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
                            <textarea id="notes" name="notes" rows="4" placeholder="Tambahkan catatan khusus tentang buyer ini..."
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
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
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Buyer Rating
                        </button>
                    </div>
                </form>
            </div>

            {{-- Preview Card --}}
            <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Preview Kalkulasi</h3>
                <div id="preview-calculation" class="text-sm text-gray-600">
                    <p>Masukkan data order untuk melihat preview kalkulasi success rate dan risk level.</p>
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
            const successRateInput = document.getElementById('success_rate');
            const riskLevelInput = document.getElementById('risk_level');

            function updatePreview() {
                const totalOrders = parseInt(totalOrdersInput.value) || 0;
                const successfulOrders = parseInt(successfulOrdersInput.value) || 0;
                const failedCodOrders = parseInt(failedCodOrdersInput.value) || 0;
                const cancelledOrders = parseInt(cancelledOrdersInput.value) || 0;

                let successRate = 0;
                let riskLevel = 'low';

                if (totalOrders > 0) {
                    successRate = (successfulOrders / totalOrders) * 100;

                    if (successRate < 60) {
                        riskLevel = 'high';
                    } else if (successRate < 80) {
                        riskLevel = 'medium';
                    } else {
                        riskLevel = 'low';
                    }
                }

                // Update hidden fields
                successRateInput.value = successRate.toFixed(2);
                riskLevelInput.value = riskLevel;

                if (totalOrders === 0) {
                    previewDiv.innerHTML =
                        '<p>Masukkan data order untuk melihat preview kalkulasi success rate dan risk level.</p>';
                    return;
                }

                let riskLevelText = 'Low Risk';
                let riskColor = 'text-green-600';
                let riskBg = 'bg-green-100';

                if (riskLevel === 'high') {
                    riskLevelText = 'High Risk';
                    riskColor = 'text-red-600';
                    riskBg = 'bg-red-100';
                } else if (riskLevel === 'medium') {
                    riskLevelText = 'Medium Risk';
                    riskColor = 'text-yellow-600';
                    riskBg = 'bg-yellow-100';
                }

                const failedOrders = failedCodOrders + cancelledOrders;

                previewDiv.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">Order Summary</h4>
                            <p class="text-sm mt-1">Total Orders: <span class="font-medium">${totalOrders}</span></p>
                            <p class="text-sm text-green-600">Sukses: ${successfulOrders}</p>
                            <p class="text-sm text-red-600">Gagal: ${failedOrders}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">Success Rate</h4>
                            <p class="text-2xl font-bold text-gray-900">${successRate.toFixed(1)}%</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-${successRate >= 80 ? 'green' : (successRate >= 60 ? 'yellow' : 'red')}-500 h-2 rounded-full" style="width: ${Math.min(successRate, 100)}%"></div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">Risk Level</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${riskBg} ${riskColor}">
                                ${riskLevelText}
                            </span>
                            ${successRate < 80 ? `<p class="text-xs text-red-600 mt-1">Tingkat kegagalan: ${(100 - successRate).toFixed(1)}%</p>` : ''}
                        </div>
                    </div>
                `;
            }

            // Add event listeners
            [totalOrdersInput, successfulOrdersInput, failedCodOrdersInput, cancelledOrdersInput].forEach(input => {
                input.addEventListener('input', updatePreview);
            });

            // Initial preview
            updatePreview();
        });
    </script>
</x-layouts.plain-app>
