{{-- resources/views/admin/buyer-rating/index.blade.php --}}
<x-layouts.plain-app>
    <div class="min-h-screen bg-neutral-50 py-xl" x-data="{
        showDeleteModal: false,
        deleteForm: null,
        showDeleteNotification(form) {
            this.deleteForm = form;
            this.showDeleteModal = true;
        },
        confirmDelete() {
            if (this.deleteForm) {
                this.deleteForm.submit();
            }
            this.showDeleteModal = false;
            this.deleteForm = null;
        }
    }">
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
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    @click="showDeleteNotification($el.closest('form'))"
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
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false">
            </div>

            {{-- Modal Content --}}
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full sm:p-6 z-10"
                    style="max-width: 28rem;">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-error-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-error-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Konfirmasi Hapus
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus buyer rating ini? Tindakan ini tidak dapat
                                    dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="confirmDelete()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-error-600 text-base font-medium text-white hover:bg-error-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-error-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus
                        </button>
                        <button type="button" @click="showDeleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes shrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }
    </style>
</x-layouts.plain-app>
