{{-- Trade Form Partial - used by create.blade.php and edit.blade.php --}}
{{-- Variables: $accounts, $tags (grouped), $trade (null for create), $selectedAccountId, $selectedTags (array of IDs) --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left Column: Core trade info --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Account + Pair + Direction --}}
        <div class="card p-5 space-y-4">
            <h3 class="text-sm font-semibold text-gray-200 flex items-center gap-2">
                <span class="w-5 h-5 rounded bg-green-500/20 text-green-400 flex items-center justify-center text-xs">1</span>
                Informasi Dasar
            </h3>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Akun Trading <span class="text-red-400">*</span></label>
                <select name="trading_account_id" id="trading_account_id" class="input-field w-full px-4 py-2.5 text-sm">
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}"
                            {{ (old('trading_account_id', $selectedAccountId ?? $trade?->trading_account_id) == $account->id) ? 'selected' : '' }}>
                            {{ $account->account_name }} ({{ $account->exchange ?? $account->currency }})
                        </option>
                    @endforeach
                </select>
                @error('trading_account_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Pair <span class="text-red-400">*</span></label>
                    <input type="text" name="pair" value="{{ old('pair', $trade?->pair) }}"
                        class="input-field w-full px-4 py-2.5 text-sm font-mono uppercase" placeholder="BTC/USDT">
                    @error('pair') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Arah <span class="text-red-400">*</span></label>
                    <div class="flex rounded-lg overflow-hidden border border-white/10">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="direction" value="long" class="sr-only peer"
                                {{ old('direction', $trade?->direction) == 'long' ? 'checked' : (old('direction') == '' && !$trade ? 'checked' : '') }}>
                            <div class="py-2.5 text-center text-sm font-semibold transition-all bg-dark-700 text-gray-400 peer-checked:bg-green-500/20 peer-checked:text-green-400">
                                📈 Long
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer border-l border-white/10">
                            <input type="radio" name="direction" value="short" class="sr-only peer"
                                {{ old('direction', $trade?->direction) == 'short' ? 'checked' : '' }}>
                            <div class="py-2.5 text-center text-sm font-semibold transition-all bg-dark-700 text-gray-400 peer-checked:bg-red-500/20 peer-checked:text-red-400">
                                📉 Short
                            </div>
                        </label>
                    </div>
                    @error('direction') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Price Info --}}
        <div class="card p-5 space-y-4">
            <h3 class="text-sm font-semibold text-gray-200 flex items-center gap-2">
                <span class="w-5 h-5 rounded bg-green-500/20 text-green-400 flex items-center justify-center text-xs">2</span>
                Harga & Quantity
            </h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Quantity (jumlah koin) <span class="text-red-400">*</span></label>
                    <input type="number" name="quantity" value="{{ old('quantity', $trade?->quantity) }}"
                        step="any" min="0" class="input-field w-full px-4 py-2.5 text-sm font-mono" placeholder="0.5">
                    @error('quantity') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Entry Price <span class="text-red-400">*</span></label>
                    <input type="number" name="entry_price" value="{{ old('entry_price', $trade?->entry_price) }}"
                        step="any" min="0" class="input-field w-full px-4 py-2.5 text-sm font-mono" placeholder="60000">
                    @error('entry_price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Stop Loss</label>
                    <input type="number" name="stop_loss" value="{{ old('stop_loss', $trade?->stop_loss) }}"
                        step="any" min="0" class="input-field w-full px-4 py-2.5 text-sm font-mono" placeholder="58000">
                    @error('stop_loss') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Take Profit</label>
                    <input type="number" name="take_profit" value="{{ old('take_profit', $trade?->take_profit) }}"
                        step="any" min="0" class="input-field w-full px-4 py-2.5 text-sm font-mono" placeholder="65000">
                    @error('take_profit') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Status & Exit --}}
        <div class="card p-5 space-y-4">
            <h3 class="text-sm font-semibold text-gray-200 flex items-center gap-2">
                <span class="w-5 h-5 rounded bg-green-500/20 text-green-400 flex items-center justify-center text-xs">3</span>
                Status Trade
            </h3>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Status <span class="text-red-400">*</span></label>
                <div class="flex rounded-lg overflow-hidden border border-white/10">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="status" value="open" id="status_open" class="sr-only peer"
                            {{ old('status', $trade?->status ?? 'open') == 'open' ? 'checked' : '' }}>
                        <div class="py-2.5 text-center text-sm font-semibold transition-all bg-dark-700 text-gray-400 peer-checked:bg-yellow-500/20 peer-checked:text-yellow-400">
                            🟡 Open
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer border-l border-white/10">
                        <input type="radio" name="status" value="closed" id="status_closed" class="sr-only peer"
                            {{ old('status', $trade?->status) == 'closed' ? 'checked' : '' }}>
                        <div class="py-2.5 text-center text-sm font-semibold transition-all bg-dark-700 text-gray-400 peer-checked:bg-blue-500/20 peer-checked:text-blue-400">
                            ✅ Closed
                        </div>
                    </label>
                </div>
            </div>

            {{-- Exit fields (show/hide via JS) --}}
            <div id="exit_fields" class="{{ old('status', $trade?->status ?? 'open') == 'closed' ? '' : 'hidden' }} space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Exit Price <span class="text-red-400">*</span></label>
                        <input type="number" name="exit_price" value="{{ old('exit_price', $trade?->exit_price) }}"
                            step="any" min="0" class="input-field w-full px-4 py-2.5 text-sm font-mono" placeholder="65000">
                        @error('exit_price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Exit Time <span class="text-red-400">*</span></label>
                        <input type="datetime-local" name="exit_time"
                            value="{{ old('exit_time', $trade?->exit_time?->format('Y-m-d\TH:i')) }}"
                            class="input-field w-full px-4 py-2.5 text-sm">
                        @error('exit_time') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-200 mb-4 flex items-center gap-2">
                <span class="w-5 h-5 rounded bg-green-500/20 text-green-400 flex items-center justify-center text-xs">4</span>
                Catatan & Screenshot
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Catatan / Lesson Learned</label>
                    <textarea name="notes" rows="4" class="input-field w-full px-4 py-2.5 text-sm resize-none"
                        placeholder="Apa yang kamu pelajari dari trade ini? Setup seperti apa? Emosi saat entry?">{{ old('notes', $trade?->notes) }}</textarea>
                    @error('notes') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Screenshot Chart</label>
                    @if($trade?->screenshot_path)
                        <div class="mb-2">
                            <img src="{{ Storage::url($trade->screenshot_path) }}" alt="Screenshot" class="h-32 rounded-lg object-cover border border-white/10">
                            <p class="text-xs text-gray-500 mt-1">Upload baru untuk mengganti.</p>
                        </div>
                    @endif
                    <input type="file" name="screenshot" accept="image/*"
                        class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-500/20 file:text-green-400 hover:file:bg-green-500/30 transition-all">
                    <p class="text-xs text-gray-600 mt-1">Maks. 5MB (PNG, JPG, WEBP)</p>
                    @error('screenshot') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Time + Tags --}}
    <div class="space-y-5">

        {{-- Entry Time --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-200 mb-4">Waktu</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Entry Time <span class="text-red-400">*</span></label>
                    <input type="datetime-local" name="entry_time"
                        value="{{ old('entry_time', $trade?->entry_time?->format('Y-m-d\TH:i')) }}"
                        class="input-field w-full px-4 py-2.5 text-sm">
                    @error('entry_time') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Tags --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-200 mb-4">Tags</h3>

            @foreach($tags as $type => $typeTags)
            <div class="mb-4">
                <div class="text-xs font-semibold uppercase tracking-wider mb-2
                    {{ $type === 'strategy' ? 'text-blue-400' : ($type === 'emotion' ? 'text-yellow-400' : 'text-red-400') }}">
                    {{ $type === 'strategy' ? '📐 Strategi' : ($type === 'emotion' ? '💭 Emosi' : '❌ Kesalahan') }}
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($typeTags as $tag)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="sr-only peer"
                            {{ in_array($tag->id, old('tags', $selectedTags ?? [])) ? 'checked' : '' }}>
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs transition-all
                            {{ $type === 'strategy' ? 'badge-strategy peer-checked:bg-blue-500/40' : ($type === 'emotion' ? 'badge-emotion peer-checked:bg-yellow-500/40' : 'badge-mistake peer-checked:bg-red-500/40') }}
                            cursor-pointer select-none">
                            {{ $tag->name }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>

<script>
    // Show/hide exit fields based on status radio
    document.querySelectorAll('input[name="status"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const exitFields = document.getElementById('exit_fields');
            exitFields.classList.toggle('hidden', this.value !== 'closed');
        });
    });
</script>
