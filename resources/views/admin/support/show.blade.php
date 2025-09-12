<x-layouts.plain-app>
    <x-slot name="title">Detail Tiket - {{ $ticket->ticket_number }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 flex items-center">
                        <i class="fas fa-ticket-alt text-primary-500 mr-3"></i>
                        {{ $ticket->ticket_number }}
                    </h1>
                    <p class="mt-2 text-neutral-600">
                        {{ $ticket->subject }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.support.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-neutral-600 text-white rounded-lg hover:bg-neutral-700 transition-colors shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Ticket Details -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-neutral-200">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-info-circle text-primary-500"></i>
                            Detail Masalah
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="prose max-w-none">
                            <p class="text-neutral-700 whitespace-pre-line">{{ $ticket->description }}</p>
                        </div>
                        
                        @if ($ticket->attachments && count($ticket->attachments) > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-neutral-700 mb-3">Lampiran:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach ($ticket->attachments as $attachment)
                                        <div class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50">
                                            <div class="flex-shrink-0 mr-3">
                                                @if (str_contains($attachment['type'], 'image'))
                                                    <i class="fas fa-image text-blue-500"></i>
                                                @elseif (str_contains($attachment['type'], 'pdf'))
                                                    <i class="fas fa-file-pdf text-red-500"></i>
                                                @else
                                                    <i class="fas fa-file text-neutral-500"></i>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-neutral-900 truncate">
                                                    {{ $attachment['name'] }}
                                                </p>
                                                <p class="text-xs text-neutral-500">
                                                    {{ number_format($attachment['size'] / 1024, 1) }} KB
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0 ml-3">
                                                <a href="{{ Storage::disk('r2')->url($attachment['path']) }}" 
                                                   target="_blank" 
                                                   class="text-primary-600 hover:text-primary-800">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Responses -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-neutral-200">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-comments text-primary-500"></i>
                            Percakapan
                            @if ($ticket->responses->count() > 0)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                    {{ $ticket->responses->count() }} respons
                                </span>
                            @endif
                        </h3>
                    </div>
                    <div class="p-6">
                        @if ($ticket->responses->count() > 0)
                            <div class="space-y-6">
                                @foreach ($ticket->responses as $response)
                                    <div class="flex {{ $response->is_admin_response ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-3xl {{ $response->is_admin_response ? 'bg-blue-50 border-blue-200' : 'bg-green-50 border-green-200' }} border rounded-lg p-4">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full {{ $response->is_admin_response ? 'bg-blue-500' : 'bg-green-500' }} flex items-center justify-center mr-3">
                                                        @if ($response->is_admin_response)
                                                            <i class="fas fa-user-cog text-white text-xs"></i>
                                                        @else
                                                            <i class="fas fa-user text-white text-xs"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium {{ $response->is_admin_response ? 'text-blue-900' : 'text-green-900' }}">
                                                            {{ $response->is_admin_response ? $response->user->name . ' (Admin)' : $response->user->name }}
                                                        </p>
                                                        <p class="text-xs {{ $response->is_admin_response ? 'text-blue-600' : 'text-green-600' }}">
                                                            {{ $response->created_at->format('d M Y H:i') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="prose prose-sm max-w-none {{ $response->is_admin_response ? 'text-blue-800' : 'text-green-800' }}">
                                                <p class="whitespace-pre-line">{{ $response->message }}</p>
                                            </div>
                                            
                                            @if ($response->attachments && count($response->attachments) > 0)
                                                <div class="mt-3 pt-3 border-t {{ $response->is_admin_response ? 'border-blue-200' : 'border-green-200' }}">
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach ($response->attachments as $attachment)
                                                            <a href="{{ Storage::disk('r2')->url($attachment['path']) }}" 
                                                               target="_blank"
                                                               class="inline-flex items-center px-2 py-1 rounded text-xs {{ $response->is_admin_response ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} transition">
                                                                <i class="fas fa-paperclip mr-1"></i>
                                                                {{ $attachment['name'] }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-neutral-300 text-4xl mb-3">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <p class="text-neutral-500">Belum ada respons untuk tiket ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Admin Response Form -->
                @if (!$ticket->isClosed())
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-neutral-200">
                        <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                            <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-reply text-primary-500"></i>
                                Respons Admin
                            </h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('admin.support.add-response', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <textarea name="message" rows="4" required
                                            placeholder="Tulis respons Anda..."
                                            class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">{{ old('message') }}</textarea>
                                        @error('message')
                                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                                Ubah Status (Opsional)
                                            </label>
                                            <select name="change_status" class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                                <option value="">Tidak mengubah status</option>
                                                <option value="in_progress">Dalam Proses</option>
                                                <option value="waiting_user">Menunggu User</option>
                                                <option value="resolved">Diselesaikan</option>
                                                <option value="closed">Ditutup</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                                Lampiran (Opsional)
                                            </label>
                                            <input type="file" name="attachments[]" multiple
                                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                                class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                            <p class="mt-1 text-xs text-neutral-500">Format: JPG, PNG, PDF, DOC, DOCX (Max 2MB, 3 file)</p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-neutral-700 mb-2">
                                            Catatan Internal (Opsional)
                                        </label>
                                        <textarea name="admin_notes" rows="2"
                                            placeholder="Catatan untuk admin lain (tidak terlihat oleh user)..."
                                            class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">{{ old('admin_notes') }}</textarea>
                                    </div>
                                    
                                    <div class="flex justify-end gap-3">
                                        <button type="submit" name="action" value="response" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-md flex items-center">
                                            <i class="fas fa-paper-plane mr-2"></i>
                                            Kirim Respons
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Quick Resolve Form -->
                @if ($ticket->canBeResolved())
                    <div class="bg-green-50 rounded-xl border border-green-200 p-6">
                        <h4 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            Selesaikan Tiket
                        </h4>
                        <form action="{{ route('admin.support.resolve', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-2">
                                        Resolusi/Solusi <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="resolution" rows="3" required
                                        placeholder="Jelaskan bagaimana masalah ini diselesaikan..."
                                        class="w-full border border-green-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">{{ old('resolution') }}</textarea>
                                    @error('resolution')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-md flex items-center">
                                        <i class="fas fa-check mr-2"></i>
                                        Selesaikan Tiket
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status & Actions Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-neutral-200">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-cogs text-primary-500"></i>
                            Status & Aksi
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Current Status -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-neutral-700">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->getStatusBadgeClass() }}">
                                {{ $ticket->getStatusLabel() }}
                            </span>
                        </div>
                        
                        <!-- Change Status Form -->
                        <form action="{{ route('admin.support.update-status', $ticket->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-1">Ubah Status:</label>
                                <select name="status" onchange="this.form.submit()" class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Terbuka</option>
                                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                                    <option value="waiting_user" {{ $ticket->status === 'waiting_user' ? 'selected' : '' }}>Menunggu User</option>
                                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Diselesaikan</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Ditutup</option>
                                </select>
                            </div>
                        </form>
                        
                        <!-- Assignment -->
                        <div class="border-t border-neutral-200 pt-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-neutral-700">Assigned To:</span>
                                @if ($ticket->assignedAdmin)
                                    <div class="flex items-center">
                                        <img class="h-6 w-6 rounded-full object-cover mr-2"
                                            src="{{ $ticket->assignedAdmin->avatar_url }}"
                                            alt="{{ $ticket->assignedAdmin->name }}">
                                        <span class="text-xs font-medium">{{ $ticket->assignedAdmin->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-neutral-400 italic">Belum di-assign</span>
                                @endif
                            </div>
                            
                            <form action="{{ route('admin.support.assign', $ticket->id) }}" method="POST">
                                @csrf
                                <div class="flex gap-2">
                                    <select name="admin_id" class="flex-1 border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                                        <option value="">-- Pilih Admin --</option>
                                        @foreach ($adminUsers as $admin)
                                            <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                                {{ $admin->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Ticket Info Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-neutral-200">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-info text-primary-500"></i>
                            Info Tiket
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-neutral-700">Prioritas:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->getPriorityBadgeClass() }}">
                                {{ $ticket->getPriorityLabel() }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-neutral-700">Kategori:</span>
                            <span class="text-sm text-neutral-900">{{ $ticket->getCategoryLabel() }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-neutral-700">Tipe:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->ticket_type === 'shipment' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ $ticket->ticket_type === 'shipment' ? 'Pengiriman' : 'Umum' }}
                            </span>
                        </div>

                        <div class="border-t border-neutral-200 pt-4">
                            <div class="text-sm font-medium text-neutral-700 mb-2">User Info:</div>
                            <div class="flex items-center mb-2">
                                <img class="h-10 w-10 rounded-full object-cover mr-3"
                                    src="{{ $ticket->user->avatar_url }}"
                                    alt="{{ $ticket->user->name }}">
                                <div>
                                    <div class="text-sm font-medium text-neutral-900">{{ $ticket->user->name }}</div>
                                    <div class="text-xs text-neutral-500">{{ $ticket->user->email }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipment Info -->
                @if ($ticket->isShipmentType() && ($ticket->pickupRequest || $ticket->tracking_number))
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-neutral-200">
                        <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                            <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-shipping-fast text-primary-500"></i>
                                Info Pengiriman
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @if ($ticket->pickupRequest)
                                <div>
                                    <span class="text-sm font-medium text-neutral-700">Kode Pickup:</span>
                                    <div class="mt-1 font-mono text-sm bg-neutral-100 px-2 py-1 rounded">
                                        {{ $ticket->pickupRequest->pickup_code }}
                                    </div>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-neutral-700">Status Pickup:</span>
                                    <div class="mt-1 text-sm capitalize">
                                        {{ $ticket->pickupRequest->status }}
                                    </div>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-neutral-700">Penerima:</span>
                                    <div class="mt-1 text-sm">
                                        {{ $ticket->pickupRequest->recipient_name }}
                                    </div>
                                </div>

                                <div>
                                    <span class="text-sm font-medium text-neutral-700">Total Amount:</span>
                                    <div class="mt-1 text-sm font-medium">
                                        Rp {{ number_format($ticket->pickupRequest->total_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endif
                            
                            @if ($ticket->tracking_number)
                                <div>
                                    <span class="text-sm font-medium text-neutral-700">Nomor Resi:</span>
                                    <div class="mt-1 font-mono text-sm bg-neutral-100 px-2 py-1 rounded">
                                        {{ $ticket->tracking_number }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow-md overflow-x-hidden border border-neutral-200">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-history text-primary-500"></i>
                            Timeline
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex items-start">
                                            <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-full ring-8 ring-white">
                                                <i class="fas fa-plus text-green-600"></i>
                                            </div>
                                            <div class="min-w-0 flex-1 pl-4">
                                                <div class="text-sm font-medium text-neutral-900">Tiket Dibuat</div>
                                                <div class="text-sm text-neutral-500">
                                                    {{ $ticket->created_at->format('d M Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                
                                @if ($ticket->assigned_to)
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex items-start">
                                                <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full ring-8 ring-white">
                                                    <i class="fas fa-user-plus text-blue-600"></i>
                                                </div>
                                                <div class="min-w-0 flex-1 pl-4">
                                                    <div class="text-sm font-medium text-neutral-900">Di-assign ke {{ $ticket->assignedAdmin->name }}</div>
                                                    <div class="text-sm text-neutral-500">
                                                        {{ $ticket->updated_at->format('d M Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                                
                                @if ($ticket->resolved_at)
                                    <li>
                                        <div class="relative">
                                            <div class="relative flex items-start">
                                                <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-full ring-8 ring-white">
                                                    <i class="fas fa-check text-green-600"></i>
                                                </div>
                                                <div class="min-w-0 flex-1 pl-4">
                                                    <div class="text-sm font-medium text-neutral-900">Tiket Diselesaikan</div>
                                                    <div class="text-sm text-neutral-500">
                                                        {{ $ticket->resolved_at->format('d M Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Resolution -->
                @if ($ticket->resolution)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-neutral-200">
                        <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                            <h3 class="text-lg font-semibold text-green-900 flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-600"></i>
                                Solusi
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-neutral-700 whitespace-pre-line">{{ $ticket->resolution }}</p>
                        </div>
                    </div>
                @endif

                <!-- Admin Notes (if any) -->
                @if ($ticket->admin_notes)
                    <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-6">
                        <h4 class="text-lg font-semibold text-yellow-900 mb-2 flex items-center">
                            <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                            Catatan Admin
                        </h4>
                        <p class="text-sm text-yellow-800 whitespace-pre-line">{{ $ticket->admin_notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.plain-app>