<x-layouts.plain-app>
    <div class="min-h-screen bg-neutral-50 py-8">
        <div class=" mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header with Gradient Background --}}
            <div class="mb-8 bg-gradient-to-r from-primary-50 to-secondary-50 rounded-xl p-6 border border-primary-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-neutral-800">Detail Buyer Rating</h1>
                        <p class="mt-1 text-sm text-neutral-600">Informasi lengkap tentang rating buyer</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.buyer-ratings.edit', $rating->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-pen mr-2"></i>
                            Edit
                        </a>
                        <a href="{{ route('admin.buyer-ratings.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-neutral-600 text-white text-sm font-medium rounded-lg hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            {{-- Risk Warning --}}
            @if ($rating->risk_warning)
                <div class="mb-6 bg-error-50 border border-error-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-error-600 mr-2"></i>
                        <span class="font-medium text-error-800">{{ $rating->risk_warning }}</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Information --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Buyer Information Card --}}
                    <div class="bg-white rounded-xl shadow-xs border border-neutral-200 p-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                            <i class="fas fa-user-circle text-primary-600 mr-2"></i>
                            Informasi Buyer
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-neutral-50 p-4 rounded-lg">
                                <label class="block text-sm font-medium text-neutral-500">Nama</label>
                                <p class="mt-1 text-lg font-semibold text-neutral-800">{{ $rating->name }}</p>
                            </div>
                            <div class="bg-neutral-50 p-4 rounded-lg">
                                <label class="block text-sm font-medium text-neutral-500">Nomor Telepon</label>
                                <p class="mt-1 text-lg font-semibold text-neutral-800">{{ $rating->phone_number }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Order Statistics Card --}}
                    <div class="bg-white rounded-xl shadow-xs border border-neutral-200 p-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                            <i class="fas fa-chart-bar text-primary-600 mr-2"></i>
                            Statistik Orders
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center bg-secondary-50 rounded-xl p-4 border border-secondary-100">
                                <i class="fas fa-shopping-bag text-secondary-600 text-2xl mb-2"></i>
                                <p class="text-2xl font-bold text-secondary-600">
                                    {{ number_format($rating->total_orders) }}</p>
                                <p class="text-sm text-neutral-600">Total Orders</p>
                            </div>
                            <div class="text-center bg-success-50 rounded-xl p-4 border border-success-100">
                                <i class="fas fa-check-circle text-success-600 text-2xl mb-2"></i>
                                <p class="text-2xl font-bold text-success-600">
                                    {{ number_format($rating->successful_orders) }}</p>
                                <p class="text-sm text-neutral-600">Sukses</p>
                            </div>
                            <div class="text-center bg-error-50 rounded-xl p-4 border border-error-100">
                                <i class="fas fa-exclamation-triangle text-error-600 text-2xl mb-2"></i>
                                <p class="text-2xl font-bold text-error-600">
                                    {{ number_format($rating->failed_cod_orders) }}</p>
                                <p class="text-sm text-neutral-600">COD Gagal</p>
                            </div>
                            <div class="text-center bg-warning-50 rounded-xl p-4 border border-warning-100">
                                <i class="fas fa-times-circle text-warning-600 text-2xl mb-2"></i>
                                <p class="text-2xl font-bold text-warning-600">
                                    {{ number_format($rating->cancelled_orders) }}</p>
                                <p class="text-sm text-neutral-600">Dibatalkan</p>
                            </div>
                        </div>

                        {{-- Success Rate Visualization --}}
                        <div class="mt-6 pt-6 border-t border-neutral-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-neutral-700">Success Rate</span>
                                <span
                                    class="text-sm font-medium text-neutral-800">{{ number_format($rating->success_rate, 1) }}%</span>
                            </div>
                            <div class="w-full bg-neutral-200 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full 
                                    @if ($rating->success_rate >= 80) bg-success-500
                                    @elseif($rating->success_rate >= 60) bg-warning-500
                                    @else bg-error-500 @endif"
                                    style="width: {{ $rating->success_rate }}%"></div>
                            </div>
                            <div class="mt-2 text-xs text-neutral-500">
                                Tingkat kegagalan: {{ number_format(100 - $rating->success_rate, 1) }}%
                            </div>
                        </div>
                    </div>

                    {{-- Notes Card --}}
                    @if ($rating->notes)
                        <div class="bg-white rounded-xl shadow-xs border border-neutral-200 p-6">
                            <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                                <i class="fas fa-sticky-note text-primary-600 mr-2"></i>
                                Catatan
                            </h3>
                            <div class="bg-neutral-50 rounded-lg p-4 border border-neutral-200">
                                <p class="text-neutral-700 whitespace-pre-wrap">{{ $rating->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Risk Level Card --}}
                    <div class="bg-white rounded-xl shadow-xs border border-neutral-200 p-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                            <i class="fas fa-shield-alt text-primary-600 mr-2"></i>
                            Risk Assessment
                        </h3>

                        <div class="text-center mb-4">
                            @if ($rating->isHighRisk())
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-error-100 rounded-full mb-3 border-2 border-error-200">
                                    <i class="fas fa-exclamation-triangle text-error-600 text-2xl"></i>
                                </div>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-error-100 text-error-800 border border-error-200">
                                    HIGH RISK
                                </span>
                            @elseif($rating->isMediumRisk())
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-warning-100 rounded-full mb-3 border-2 border-warning-200">
                                    <i class="fas fa-exclamation-circle text-warning-600 text-2xl"></i>
                                </div>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-warning-100 text-warning-800 border border-warning-200">
                                    MEDIUM RISK
                                </span>
                            @else
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-success-100 rounded-full mb-3 border-2 border-success-200">
                                    <i class="fas fa-check-circle text-success-600 text-2xl"></i>
                                </div>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success-100 text-success-800 border border-success-200">
                                    LOW RISK
                                </span>
                            @endif
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between bg-neutral-50 p-3 rounded-lg">
                                <span class="text-neutral-600">Success Rate:</span>
                                <span
                                    class="font-medium text-neutral-800">{{ number_format($rating->success_rate, 1) }}%</span>
                            </div>
                            <div class="flex justify-between bg-neutral-50 p-3 rounded-lg">
                                <span class="text-neutral-600">Failure Rate:</span>
                                <span
                                    class="font-medium text-neutral-800">{{ number_format(100 - $rating->success_rate, 1) }}%</span>
                            </div>
                            <div class="flex justify-between bg-neutral-50 p-3 rounded-lg">
                                <span class="text-neutral-600">Total Failed Orders:</span>
                                <span
                                    class="font-medium text-neutral-800">{{ number_format($rating->failed_cod_orders + $rating->cancelled_orders) }}</span>
                            </div>
                        </div>

                        {{-- Risk Level Guidelines --}}
                        <div class="mt-6 pt-6 border-t border-neutral-200">
                            <h4 class="text-sm font-medium text-neutral-800 mb-3">Risk Level Guidelines</h4>
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center bg-success-50 p-2 rounded-lg">
                                    <div class="w-3 h-3 bg-success-500 rounded-full mr-2"></div>
                                    <span class="text-neutral-700">Low Risk: ≥80% success rate</span>
                                </div>
                                <div class="flex items-center bg-warning-50 p-2 rounded-lg">
                                    <div class="w-3 h-3 bg-warning-500 rounded-full mr-2"></div>
                                    <span class="text-neutral-700">Medium Risk: 60-79% success rate</span>
                                </div>
                                <div class="flex items-center bg-error-50 p-2 rounded-lg">
                                    <div class="w-3 h-3 bg-error-500 rounded-full mr-2"></div>
                                    <span class="text-neutral-700">High Risk: <60% success rate</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Timestamp Card --}}
                    <div class="bg-white rounded-xl shadow-xs border border-neutral-200 p-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-primary-600 mr-2"></i>
                            Informasi Sistem
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="bg-neutral-50 p-3 rounded-lg">
                                <span class="text-neutral-600">Dibuat:</span>
                                <p class="font-medium text-neutral-800">
                                    {{ $rating->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="bg-neutral-50 p-3 rounded-lg">
                                <span class="text-neutral-600">Terakhir diupdate:</span>
                                <p class="font-medium text-neutral-800">
                                    {{ $rating->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="bg-neutral-50 p-3 rounded-lg">
                                <span class="text-neutral-600">ID:</span>
                                <p class="font-medium text-neutral-500">#{{ $rating->id }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Card --}}
                    <div class="bg-white rounded-xl shadow-xs border border-neutral-200 p-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                            <i class="fas fa-cog text-primary-600 mr-2"></i>
                            Actions
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.buyer-ratings.edit', $rating->id) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-pen mr-2"></i>
                                Edit Rating
                            </a>
                            <form method="POST" action="{{ route('admin.buyer-ratings.destroy', $rating->id) }}"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus rating buyer ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-error-600 text-white text-sm font-medium rounded-lg hover:bg-error-700 focus:outline-none focus:ring-2 focus:ring-error-500 focus:ring-offset-2 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    Hapus Rating
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.plain-app>
