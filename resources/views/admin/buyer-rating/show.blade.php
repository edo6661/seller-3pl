{{-- resources/views/admin/buyer-rating/show.blade.php --}}
<x-layouts.plain-app>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Detail Buyer Rating</h1>
                        <p class="mt-2 text-gray-600">Informasi lengkap tentang rating buyer</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.buyer-ratings.edit', $rating->id) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                        <a href="{{ route('admin.buyer-ratings.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            {{-- Risk Warning --}}
            @if($rating->risk_warning)
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span class="font-medium text-red-800">{{ $rating->risk_warning }}</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Information --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Buyer Information Card --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Buyer</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Nama</label>
                                <p class="mt-1 text-lg font-medium text-gray-900">{{ $rating->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Nomor Telepon</label>
                                <p class="mt-1 text-lg font-medium text-gray-900">{{ $rating->phone_number }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Order Statistics Card --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Orders</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    <p class="text-2xl font-bold text-blue-600">{{ number_format($rating->total_orders) }}</p>
                                    <p class="text-sm text-gray-600">Total Orders</p>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="bg-green-50 rounded-lg p-4">
                                    <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-2xl font-bold text-green-600">{{ number_format($rating->successful_orders) }}</p>
                                    <p class="text-sm text-gray-600">Sukses</p>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="bg-red-50 rounded-lg p-4">
                                    <svg class="w-8 h-8 text-red-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-2xl font-bold text-red-600">{{ number_format($rating->failed_cod_orders) }}</p>
                                    <p class="text-sm text-gray-600">COD Gagal</p>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="bg-yellow-50 rounded-lg p-4">
                                    <svg class="w-8 h-8 text-yellow-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($rating->cancelled_orders) }}</p>
                                    <p class="text-sm text-gray-600">Dibatalkan</p>
                                </div>
                            </div>
                        </div>

                        {{-- Success Rate Visualization --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Success Rate</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($rating->success_rate, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-{{ $rating->success_rate >= 80 ? 'green' : ($rating->success_rate >= 60 ? 'yellow' : 'red') }}-500 h-3 rounded-full transition-all duration-300" 
                                     style="width: {{ $rating->success_rate }}%"></div>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                Tingkat kegagalan: {{ number_format(100 - $rating->success_rate, 1) }}%
                            </div>
                        </div>
                    </div>

                    {{-- Notes Card --}}
                    @if($rating->notes)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Catatan</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $rating->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Risk Level Card --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Risk Assessment</h3>
                        
                        <div class="text-center mb-4">
                            @if($rating->isHighRisk())
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    HIGH RISK
                                </span>
                            @elseif($rating->isMediumRisk())
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    MEDIUM RISK
                                </span>
                            @else
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    LOW RISK
                                </span>
                            @endif
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Success Rate:</span>
                                <span class="font-medium">{{ number_format($rating->success_rate, 1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Failure Rate:</span>
                                <span class="font-medium">{{ number_format(100 - $rating->success_rate, 1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Failed Orders:</span>
                                <span class="font-medium">{{ number_format($rating->failed_cod_orders + $rating->cancelled_orders) }}</span>
                            </div>
                        </div>

                        {{-- Risk Level Guidelines --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Risk Level Guidelines</h4>
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                    <span class="text-gray-600">Low Risk: ≥80% success rate</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                    <span class="text-gray-600">Medium Risk: 60-79% success rate</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                    <span class="text-gray-600">High Risk: <60% success rate</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Timestamp Card --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Sistem</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-600">Dibuat:</span>
                                <p class="font-medium">{{ $rating->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Terakhir diupdate:</span>
                                <p class="font-medium">{{ $rating->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">ID:</span>
                                <p class="font-medium text-gray-500">#{{ $rating->id }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Card --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.buyer-ratings.edit', $rating->id) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Rating
                            </a>
                            <form method="POST" action="{{ route('admin.buyer-ratings.destroy', $rating->id) }}" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus rating buyer ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
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