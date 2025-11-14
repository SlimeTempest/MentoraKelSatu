@extends('layouts.app', ['title' => 'Topup Saldo'])

@section('content')
    <div class="mx-auto max-w-2xl rounded-lg bg-white p-8 shadow">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Topup Saldo</h1>

        <form action="{{ route('topups.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="amount" class="mb-2 block text-sm font-medium text-gray-700">Jumlah Topup</label>
                <input
                    type="number"
                    id="amount"
                    name="amount"
                    value="{{ old('amount') }}"
                    min="10000"
                    step="1000"
                    required
                    class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                    placeholder="Minimum Rp 10.000"
                >
                <p class="mt-1 text-xs text-gray-500">Minimum topup: Rp 10.000</p>
                @error('amount')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Rekening Tujuan</label>
                
                <div class="mb-3 space-y-3">
                    <p class="text-xs font-medium text-gray-600">Pilih rekening yang Anda gunakan untuk transfer:</p>
                    
                    <label class="flex cursor-pointer items-center justify-between rounded-lg border-2 border-gray-200 bg-white p-4 transition hover:border-indigo-300 hover:bg-indigo-50 {{ old('rekening_tujuan') === 'BCA: ' . $bcaNumber . ' a.n. ' . $bcaName ? 'border-indigo-500 bg-indigo-50' : '' }}">
                        <div class="flex items-center gap-3">
                            <input
                                type="radio"
                                name="rekening_tujuan"
                                value="BCA: {{ $bcaNumber }} a.n. {{ $bcaName }}"
                                {{ old('rekening_tujuan') === 'BCA: ' . $bcaNumber . ' a.n. ' . $bcaName ? 'checked' : '' }}
                                required
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500"
                            >
                            <div>
                                <p class="text-sm font-medium text-gray-900">BCA</p>
                                <p class="text-xs text-gray-600" id="rekening-bca">{{ $bcaNumber }}</p>
                                <p class="text-xs text-gray-500">a.n. {{ $bcaName }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            onclick="copyToClipboard('{{ $bcaNumber }}', this); event.stopPropagation();"
                            class="rounded border border-blue-300 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100"
                        >
                            Copy
                        </button>
                    </label>
                    
                    <label class="flex cursor-pointer items-center justify-between rounded-lg border-2 border-gray-200 bg-white p-4 transition hover:border-indigo-300 hover:bg-indigo-50 {{ old('rekening_tujuan') === 'Mandiri: ' . $mandiriNumber . ' a.n. ' . $mandiriName ? 'border-indigo-500 bg-indigo-50' : '' }}">
                        <div class="flex items-center gap-3">
                            <input
                                type="radio"
                                name="rekening_tujuan"
                                value="Mandiri: {{ $mandiriNumber }} a.n. {{ $mandiriName }}"
                                {{ old('rekening_tujuan') === 'Mandiri: ' . $mandiriNumber . ' a.n. ' . $mandiriName ? 'checked' : '' }}
                                required
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500"
                            >
                            <div>
                                <p class="text-sm font-medium text-gray-900">Mandiri</p>
                                <p class="text-xs text-gray-600" id="rekening-mandiri">{{ $mandiriNumber }}</p>
                                <p class="text-xs text-gray-500">a.n. {{ $mandiriName }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            onclick="copyToClipboard('{{ $mandiriNumber }}', this); event.stopPropagation();"
                            class="rounded border border-blue-300 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100"
                        >
                            Copy
                        </button>
                    </label>
                </div>
                
                <p class="mt-1 text-xs text-gray-500">Pastikan transfer ke rekening yang dipilih dan sertakan bukti transfer.</p>
                @error('rekening_tujuan')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="bukti_pembayaran" class="mb-2 block text-sm font-medium text-gray-700">Bukti Pembayaran</label>
                <input
                    type="file"
                    id="bukti_pembayaran"
                    name="bukti_pembayaran"
                    accept="image/jpeg,image/png,image/jpg"
                    required
                    class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                >
                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG (Maks. 2MB)</p>
                @error('bukti_pembayaran')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                <p class="text-sm text-amber-800">
                    <strong>Perhatian:</strong> Topup akan diproses setelah admin menyetujui bukti pembayaran Anda. Proses biasanya memakan waktu 1-2 hari kerja.
                </p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Kirim Permintaan Topup
                </button>
                <a href="{{ route('topups.index') }}" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        function copyToClipboard(text, buttonElement) {
            navigator.clipboard.writeText(text).then(function() {
                const originalText = buttonElement.textContent;
                buttonElement.textContent = 'Copied!';
                buttonElement.classList.remove('text-blue-700', 'hover:bg-blue-100', 'border-blue-300');
                buttonElement.classList.add('text-green-700', 'bg-green-50', 'border-green-300');
                
                setTimeout(function() {
                    buttonElement.textContent = originalText;
                    buttonElement.classList.remove('text-green-700', 'bg-green-50', 'border-green-300');
                    buttonElement.classList.add('text-blue-700', 'hover:bg-blue-100', 'border-blue-300');
                }, 2000);
            }).catch(function(err) {
                alert('Gagal menyalin: ' + err);
            });
        }

        // Update border saat radio dipilih
        document.querySelectorAll('input[name="rekening_tujuan"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.querySelectorAll('label[for], label').forEach(function(label) {
                    if (label.querySelector('input[name="rekening_tujuan"]')) {
                        if (label.querySelector('input[name="rekening_tujuan"]').checked) {
                            label.classList.add('border-indigo-500', 'bg-indigo-50');
                            label.classList.remove('border-gray-200');
                        } else {
                            label.classList.remove('border-indigo-500', 'bg-indigo-50');
                            label.classList.add('border-gray-200');
                        }
                    }
                });
            });
        });
    </script>
@endsection

