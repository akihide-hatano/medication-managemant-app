<x-app-layout>
    <div class="max-w-xl mx-auto mt-8">
        <x-flash-message />
        <h1 class="text-2xl font-bold mb-6 text-center">時間帯タグを登録</h1>

        <form method="POST" action="{{ route('timing-tags.store') }}" class="space-y-5">
        @csrf

            {{-- タグ名 --}}
            <div>
                <label class="block font-medium mb-1">タグ名 <span class="text-red-600">*</span></label>
                <input type="text" name="timing_name"
                    value="{{ old('timing_name') }}"
                    class="w-full border rounded-lg px-3 py-2"
                    required>
                @error('timing_name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 基準時刻 --}}
            <div>
                <label class="block font-medium mb-1">基準時刻 <span class="text-red-600">*</span></label>
                <input type="time" name="base_time"
                    value="{{ old('base_time') }}"
                    class="w-full border rounded-lg px-3 py-2"
                    required>
                @error('base_time')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ボタン --}}
                <div class="flex justify-center gap-3">
                        <button type="submit"
                                class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">
                        登録する
                        </button>
                        <a href="{{ route('timing-tags.index') }}"
                        class="bg-gray-300 text-gray-800 px-5 py-2 rounded-lg hover:bg-gray-400">
                        一覧へ戻る
                        </a>
                </div>
        </form>
    </div>
</x-app-layout>
