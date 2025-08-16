<x-layouts.plain-app>
    <x-slot:title>Ajukan Ulang Verifikasi</x-slot:title>
    <div class="bg-gray-50 py-12">
        <div class="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <form action="{{ route('profile.verification.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="overflow-hidden rounded-lg bg-white shadow-md">
                    <div class="px-6 py-5">
                        <h3 class="text-xl font-semibold leading-6 text-gray-900">Ajukan Ulang Verifikasi</h3>
                        <p class="mt-1 max-w-5xl text-sm text-gray-500">Unggah kembali dokumen Anda sesuai catatan dari admin.</p>
                    </div>
                    <div class="border-t border-gray-200 px-6 py-5">
                        <div class="rounded-md bg-red-50 p-4">
                            <h4 class="text-sm font-bold text-red-800">Alasan Penolakan Sebelumnya</h4>
                            <p class="mt-2 text-sm italic text-red-700">"{{ $profileData['seller_profile']->verification_notes }}"</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 px-6 py-5">
                        <div class="grid grid-cols-1 gap-y-8">
                            <div>
                                <label for="ktp_image" class="block text-sm font-medium text-gray-700">Foto KTP Baru <span class="text-red-600">*</span></label>
                                <input type="file" name="ktp_image" id="ktp_image" required class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-violet-50 file:py-2 file:px-4 file:text-sm file:font-semibold file:text-violet-700 hover:file:bg-violet-100"/>
                                @error('ktp_image')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                <img id="ktp-preview" src="https://ui-avatars.com/api/?name=KTP" alt="Pratinjau KTP" class="mt-4 h-auto w-full max-w-xs rounded-md border object-cover">
                            </div>
                            <div>
                                <label for="passbook_image" class="block text-sm font-medium text-gray-700">Foto Buku Tabungan Baru <span class="text-red-600">*</span></label>
                                <input type="file" name="passbook_image" id="passbook_image" required class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-violet-50 file:py-2 file:px-4 file:text-sm file:font-semibold file:text-violet-700 hover:file:bg-violet-100"/>
                                @error('passbook_image')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                <img id="passbook-preview" src="https://ui-avatars.com/api/?name=Buku+Tabungan" alt="Pratinjau Buku Tabungan" class="mt-4 h-auto w-full max-w-xs rounded-md border object-cover">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-x-3">
                        <a href="{{ route('profile.index') }}" class="rounded-md bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Kembali</a>
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Kirim Pengajuan Ulang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        // TAMBAHKAN PEMBUNGKUS INI
        document.addEventListener('DOMContentLoaded', function () {
            // Skrip pratinjau gambar tetap sama
            function setupImagePreview(inputId, previewId) {
                const inputElement = document.getElementById(inputId);
                const previewElement = document.getElementById(previewId);
                if (!inputElement || !previewElement) return;
                inputElement.addEventListener('change', (event) => {
                    const [file] = event.target.files;
                    if (file) previewElement.src = URL.createObjectURL(file);
                });
            }
            setupImagePreview('ktp_image', 'ktp-preview');
            setupImagePreview('passbook_image', 'passbook-preview');
        }); // TUTUP PEMBUNGKUS
    </script>
</x-layouts.plain-app>