<x-app-layout>
    <div class="flex justify-center">
        <div class="w-full max-w-2xl bg-white shadow rounded-lg p-6 mt-8">
            <h1 class="text-2xl font-bold text-center mb-6">薬を編集</h1>

            {{-- フラッシュメッセージ（更新完了時に出る） --}}
            @if (session('ok'))
                <div id="flash" class="mb-4 bg-green-100 text-green-800 px-3 py-2 rounded text-center">
                    {{ session('ok') }}
                </div>
                <script>setTimeout(()=>document.getElementById('flash')?.remove(),3000)</script>
            @endif

            <form method="POST" action="{{ route('medications.update', $medication) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- 薬品名 --}}
                <div>
                    <label class="block font-medium mb-1">薬品名 <span class="text-red-600">*</span></label>
                    <input type="text" name="medication_name"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        value="{{ old('medication_name', $medication->medication_name) }}" required>
                    @error('medication_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 用量 --}}
                <div>
                    <label class="block font-medium mb-1">用量</label>
                    <input type="text" name="dosage"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        value="{{ old('dosage', $medication->dosage) }}">
                    @error('dosage')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 効果 --}}
                <div>
                    <label class="block font-medium mb-1">効果</label>
                    <textarea name="effects" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('effects', $medication->effects) }}</textarea>
                    @error('effects')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 副作用 --}}
                <div>
                    <label class="block font-medium mb-1">副作用</label>
                    <textarea name="side_effects" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('side_effects', $medication->side_effects) }}</textarea>
                    @error('side_effects')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- メモ --}}
                <div>
                    <label class="block font-medium mb-1">メモ</label>
                    <textarea name="notes" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $medication->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ボタン --}}
                <div class="flex justify-center gap-4">
                    <button type="submit"
                        class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700">
                        更新する
                    </button>
                    <a href="{{ route('medications.index') }}"
                        class="bg-gray-300 text-gray-800 font-semibold px-5 py-2 rounded-lg hover:bg-gray-400">
                        一覧に戻る
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
