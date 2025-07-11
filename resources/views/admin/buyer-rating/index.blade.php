{{-- resources/views/admin/buyer-rating/index.blade.php --}}
<x-layouts.plain-app>
    <div class="min-h-screen bg-neutral-50 py-xl">
        <div class=" mx-auto px-md sm:px-lg lg:px-xl">
            {{-- Header --}}
            <div class="mb-2xl">
                <h1 class="text-3xl font-bold text-neutral-900">Buyer Rating Management</h1>
                <p class="mt-sm text-neutral-600">Kelola dan pantau rating buyer untuk mengurangi risiko bisnis</p>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-lg mb-2xl">
                <div class="bg-white rounded-lg shadow-sm border border-neutral-200 p-lg">
                    <div class="flex items-center">
                        <div class="p-sm rounded-full bg-secondary-100">
                            <i class="fas fa-users text-secondary-600 text-xl"></i>
                        </div>
                        <div class="ml-md">
                            <p class="text-sm font-medium text-neutral-600">Total Buyer</p>
                            <p class="text-2xl font-semibold text-neutral-900">{{ number_format($stats['total']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-neutral-200 p-lg">
                    <div class="flex items-center">
                        <div class="p-sm rounded-full bg-error-100">
                            <i class="fas fa-exclamation-triangle text-error-600 text-xl"></i>
                        </div>
                        <div class="ml-md">
                            <p class="text-sm font-medium text-neutral-600">High Risk</p>
                            <p class="text-2xl font-semibold text-error-600">{{ number_format($stats['high_risk']) }}
                            </p>
                            <p class="text-xs text-neutral-500">{{ $stats['high_risk_percentage'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-neutral-200 p-lg">
                    <div class="flex items-center">
                        <div class="p-sm rounded-full bg-warning-100">
                            <i class="fas fa-exclamation-circle text-warning-600 text-xl"></i>
                        </div>
                        <div class="ml-md">
                            <p class="text-sm font-medium text-neutral-600">Medium Risk</p>
                            <p class="text-2xl font-semibold text-warning-600">
                                {{ number_format($stats['medium_risk']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-neutral-200 p-lg">
                    <div class="flex items-center">
                        <div class="p-sm rounded-full bg-success-100">
                            <i class="fas fa-check-circle text-success-600 text-xl"></i>
                        </div>
                        <div class="ml-md">
                            <p class="text-sm font-medium text-neutral-600">Success Rate</p>
                            <p class="text-2xl font-semibold text-success-600">
                                {{ number_format($stats['average_success_rate'], 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search and Actions --}}
            <div class="bg-white rounded-lg shadow-sm border border-neutral-200 p-lg mb-lg">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-md">
                    <div class="">
                        <form method="GET" action="{{ route('admin.buyer-ratings.index') }}">
                            <div class="relative">
                                <input type="text" name="search" value="{{ $search }}"
                                    placeholder="Cari Buyer"
                                    class="w-full pl-10 pr-md py-sm border border-neutral-300 rounded-md focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 text-sm">
                                <div class="absolute inset-y-0 left-0 pl-sm flex items-center pointer-events-none">
                                    <i class="fas fa-search text-neutral-400"></i>
                                </div>
                            </div>
                        </form>
                    </div>
                    <a href="{{ route('admin.buyer-ratings.create') }}"
                        class="inline-flex items-center px-md py-sm bg-secondary text-white text-sm font-medium rounded-md hover:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-plus mr-sm"></i>
                        Tambah Buyer Rating
                    </a>
                </div>
            </div>

            {{-- High Risk Alert --}}
            @if ($highRiskBuyers->count() > 0)
                <div class="bg-error-50 border border-error-200 rounded-lg p-md mb-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-error-600 mr-sm"></i>
                        <h3 class="text-sm font-medium text-error-800">Peringatan: {{ $highRiskBuyers->count() }} Buyer
                            High Risk</h3>
                    </div>
                    <div class="mt-sm text-sm text-error-700">
                        Harap berhati-hati dengan buyer berikut:
                        @foreach ($highRiskBuyers->take(3) as $buyer)
                            <span class="font-medium">{{ $buyer->name }}</span>
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                        @if ($highRiskBuyers->count() > 3)
                            dan {{ $highRiskBuyers->count() - 3 }} lainnya
                        @endif
                    </div>
                </div>
            @endif

            {{-- Data Table --}}
            <div class="bg-white rounded-lg shadow-sm border border-neutral-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th
                                    class="px-lg py-sm text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Buyer Info</th>
                                <th
                                    class="px-lg py-sm text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Statistik Orders</th>
                                <th
                                    class="px-lg py-sm text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Success Rate</th>
                                <th
                                    class="px-lg py-sm text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Risk Level</th>
                                <th
                                    class="px-lg py-sm text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-200">
                            @forelse($ratings as $rating)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-lg py-md whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-secondary-100 flex items-center justify-center">
                                                    <i class="fas fa-user text-secondary-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-md">
                                                <div class="text-sm font-medium text-neutral-900">{{ $rating->name }}
                                                </div>
                                                <div class="text-sm text-neutral-500">
                                                    <i class="fas fa-phone mr-1"></i>{{ $rating->phone_number }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-lg py-md whitespace-nowrap">
                                        <div class="text-sm text-neutral-900">
                                            <div class="flex items-center mb-1">
                                                <i class="fas fa-shopping-cart text-neutral-400 mr-1"></i>
                                                <span>Total: <span
                                                        class="font-medium">{{ $rating->total_orders }}</span></span>
                                            </div>
                                            <div class="flex items-center mb-1">
                                                <i class="fas fa-check-circle text-success-600 mr-1"></i>
                                                <span class="text-success-600">Sukses:
                                                    {{ $rating->successful_orders }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-times-circle text-error-600 mr-1"></i>
                                                <span class="text-error-600">Gagal:
                                                    {{ $rating->failed_cod_orders + $rating->cancelled_orders }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-lg py-md whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-neutral-900">
                                                    {{ number_format($rating->success_rate, 1) }}%</div>
                                                <div class="w-full bg-neutral-200 rounded-full h-2 mt-1">
                                                    @if ($rating->success_rate >= 80)
                                                        <div class="bg-success-500 h-2 rounded-full"
                                                            style="width: {{ $rating->success_rate }}%"></div>
                                                    @elseif($rating->success_rate >= 60)
                                                        <div class="bg-warning-500 h-2 rounded-full"
                                                            style="width: {{ $rating->success_rate }}%"></div>
                                                    @else
                                                        <div class="bg-error-500 h-2 rounded-full"
                                                            style="width: {{ $rating->success_rate }}%"></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-lg py-md whitespace-nowrap">
                                        @if ($rating->isHighRisk())
                                            <span
                                                class="inline-flex items-center px-sm py-xs rounded-full text-xs font-medium bg-error-100 text-error-700">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                High Risk
                                            </span>
                                        @elseif($rating->isMediumRisk())
                                            <span
                                                class="inline-flex items-center px-sm py-xs rounded-full text-xs font-medium bg-warning-100 text-warning-700">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                Medium Risk
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-sm py-xs rounded-full text-xs font-medium bg-success-100 text-success-700">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Low Risk
                                            </span>
                                        @endif
                                        @if ($rating->risk_warning)
                                            <div class="text-xs text-error-600 mt-1">
                                                <i class="fas fa-info-circle mr-1"></i>{{ $rating->risk_warning }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-lg py-md whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-sm">
                                            <a href="{{ route('admin.buyer-ratings.show', $rating->id) }}"
                                                class="text-secondary-600 hover:text-secondary-900 transition-colors p-1 rounded hover:bg-secondary-50"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.buyer-ratings.edit', $rating->id) }}"
                                                class="text-primary-600 hover:text-primary-900 transition-colors p-1 rounded hover:bg-primary-50"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('admin.buyer-ratings.destroy', $rating->id) }}"
                                                class="inline-block"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus rating buyer ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-error-600 hover:text-error-900 transition-colors p-1 rounded hover:bg-error-50"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-lg py-12 text-center">
                                        <div class="text-neutral-500">
                                            <i class="fas fa-inbox text-neutral-400 text-5xl mb-md"></i>
                                            <p class="text-sm">
                                                {{ $search ? 'Tidak ada hasil pencarian untuk "' . $search . '"' : 'Belum ada data buyer rating' }}
                                            </p>
                                            @if (!$search)
                                                <a href="{{ route('admin.buyer-ratings.create') }}"
                                                    class="inline-flex items-center mt-md px-md py-sm bg-secondary text-white text-sm font-medium rounded-md hover:bg-secondary-700 transition-colors">
                                                    <i class="fas fa-plus mr-sm"></i>
                                                    Tambah Buyer Rating Pertama
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($ratings->hasPages())
                    <div class="bg-white px-md py-sm border-t border-neutral-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                @if ($ratings->onFirstPage())
                                    <span
                                        class="relative inline-flex items-center px-md py-sm border border-neutral-300 text-sm font-medium rounded-md text-neutral-500 bg-white cursor-default">
                                        Sebelumnya
                                    </span>
                                @else
                                    <a href="{{ $ratings->previousPageUrl() }}"
                                        class="relative inline-flex items-center px-md py-sm border border-neutral-300 text-sm font-medium rounded-md text-neutral-700 bg-white hover:bg-neutral-50 transition-colors">
                                        Sebelumnya
                                    </a>
                                @endif

                                @if ($ratings->hasMorePages())
                                    <a href="{{ $ratings->nextPageUrl() }}"
                                        class="ml-sm relative inline-flex items-center px-md py-sm border border-neutral-300 text-sm font-medium rounded-md text-neutral-700 bg-white hover:bg-neutral-50 transition-colors">
                                        Selanjutnya
                                    </a>
                                @else
                                    <span
                                        class="ml-sm relative inline-flex items-center px-md py-sm border border-neutral-300 text-sm font-medium rounded-md text-neutral-500 bg-white cursor-default">
                                        Selanjutnya
                                    </span>
                                @endif
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-neutral-700">
                                        Menampilkan
                                        <span class="font-medium">{{ $ratings->firstItem() }}</span>
                                        sampai
                                        <span class="font-medium">{{ $ratings->lastItem() }}</span>
                                        dari
                                        <span class="font-medium">{{ $ratings->total() }}</span>
                                        hasil
                                    </p>
                                </div>
                                <div>
                                    {{ $ratings->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="fixed top-4 right-4 z-50 max-w-sm w-full bg-success-50 border border-success-200 rounded-lg shadow-lg"
                    x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                    <div class="p-md">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-success-600"></i>
                            </div>
                            <div class="ml-sm">
                                <p class="text-sm font-medium text-success-800">{{ session('success') }}</p>
                            </div>
                            <div class="ml-auto pl-sm">
                                <button @click="show = false" class="text-success-600 hover:text-success-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="fixed top-4 right-4 z-50 max-w-sm w-full bg-error-50 border border-error-200 rounded-lg shadow-lg"
                    x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                    <div class="p-md">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-error-600"></i>
                            </div>
                            <div class="ml-sm">
                                <p class="text-sm font-medium text-error-800">{{ session('error') }}</p>
                            </div>
                            <div class="ml-auto pl-sm">
                                <button @click="show = false" class="text-error-600 hover:text-error-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.plain-app>
