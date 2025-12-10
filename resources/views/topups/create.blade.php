@extends('layouts.app', ['title' => 'Topup Saldo'])

@section('content')
    <div class="mx-auto max-w-2xl rounded-lg border border-gray-700 bg-gray-800 p-6 sm:p-8 shadow-lg">
        <h1 class="mb-6 text-2xl font-bold text-gray-100">Topup Saldo</h1>

        <form action="{{ route('topups.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="amount" class="mb-2 block text-sm font-medium text-gray-300">Jumlah Topup</label>
                <input
                    type="number"
                    id="amount"
                    name="amount"
                    value="{{ old('amount') }}"
                    min="10000"
                    step="1000"
                    required
                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors"
                    placeholder="Minimum Rp 10.000"
                >
                <p class="mt-1 text-xs text-gray-400">Minimum topup: Rp 10.000</p>
                @error('amount')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-300">Rekening Tujuan</label>
                
                <div class="mb-3 space-y-3">
                    <p class="text-xs font-medium text-gray-400">Pilih rekening yang Anda gunakan untuk transfer:</p>
                    
                    <label class="flex cursor-pointer items-center justify-between rounded-lg border-2 border-gray-600 bg-gray-700 p-4 transition hover:border-indigo-500 hover:bg-indigo-500/10 {{ old('rekening_tujuan') === 'BCA: ' . $bcaNumber . ' a.n. ' . $bcaName ? 'border-indigo-500 bg-indigo-500/20' : '' }}">
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
                                <p class="text-sm font-medium text-gray-100">BCA</p>
                                <p class="text-xs text-gray-300 font-mono" id="rekening-bca">{{ $bcaNumber }}</p>
                                <p class="text-xs text-gray-400">a.n. {{ $bcaName }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            onclick="copyToClipboard('{{ $bcaNumber }}', this); event.stopPropagation();"
                            class="rounded-lg border border-indigo-500/30 bg-indigo-500/20 px-3 py-1.5 text-xs font-medium text-indigo-300 hover:bg-indigo-500/30 transition-colors"
                        >
                            Copy
                        </button>
                    </label>
                    
                    <label class="flex cursor-pointer items-center justify-between rounded-lg border-2 border-gray-600 bg-gray-700 p-4 transition hover:border-indigo-500 hover:bg-indigo-500/10 {{ old('rekening_tujuan') === 'Mandiri: ' . $mandiriNumber . ' a.n. ' . $mandiriName ? 'border-indigo-500 bg-indigo-500/20' : '' }}">
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
                                <p class="text-sm font-medium text-gray-100">Mandiri</p>
                                <p class="text-xs text-gray-300 font-mono" id="rekening-mandiri">{{ $mandiriNumber }}</p>
                                <p class="text-xs text-gray-400">a.n. {{ $mandiriName }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            onclick="copyToClipboard('{{ $mandiriNumber }}', this); event.stopPropagation();"
                            class="rounded-lg border border-indigo-500/30 bg-indigo-500/20 px-3 py-1.5 text-xs font-medium text-indigo-300 hover:bg-indigo-500/30 transition-colors"
                        >
                            Copy
                        </button>
                    </label>
                </div>
                
                <p class="mt-1 text-xs text-gray-400">Pastikan transfer ke rekening yang dipilih dan sertakan bukti transfer.</p>
                @error('rekening_tujuan')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="bukti_pembayaran" class="mb-2 block text-sm font-medium text-gray-300">Bukti Pembayaran</label>
                <input
                    type="file"
                    id="bukti_pembayaran"
                    name="bukti_pembayaran"
                    accept="image/jpeg,image/png,image/jpg"
                    required
                    class="block w-full text-sm text-gray-400 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-50 hover:file:bg-indigo-500 transition-colors"
                >
                <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG (Maks. 2MB)</p>
                @error('bukti_pembayaran')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 p-4">
                <p class="text-sm text-amber-300">
                    <strong>Perhatian:</strong> Topup akan diproses setelah admin menyetujui bukti pembayaran Anda. Proses biasanya memakan waktu 1-2 hari kerja.
                </p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-105">
                    Kirim Permintaan Topup
                </button>
                <a href="{{ route('topups.index') }}" class="rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-center text-sm font-semibold text-gray-300 hover:bg-gray-600 hover:text-gray-100 transition-all duration-200">
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
                buttonElement.classList.remove('text-indigo-300', 'bg-indigo-500/20', 'border-indigo-500/30');
                buttonElement.classList.add('text-green-300', 'bg-green-500/20', 'border-green-500/30');
                
                setTimeout(function() {
                    buttonElement.textContent = originalText;
                    buttonElement.classList.remove('text-green-300', 'bg-green-500/20', 'border-green-500/30');
                    buttonElement.classList.add('text-indigo-300', 'bg-indigo-500/20', 'border-indigo-500/30');
                }, 2000);
            }).catch(function(err) {
                showToast('Gagal menyalin: ' + err, 'error');
            });
        }

        // Update border saat radio dipilih
        document.querySelectorAll('input[name="rekening_tujuan"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.querySelectorAll('label[for], label').forEach(function(label) {
                    if (label.querySelector('input[name="rekening_tujuan"]')) {
                        if (label.querySelector('input[name="rekening_tujuan"]').checked) {
                            label.classList.add('border-indigo-500', 'bg-indigo-500/20');
                            label.classList.remove('border-gray-600', 'bg-gray-700');
                        } else {
                            label.classList.remove('border-indigo-500', 'bg-indigo-500/20');
                            label.classList.add('border-gray-600', 'bg-gray-700');
                        }
                    }
                });
            });
        });
    </script>
@endsection

