<x-app-layout>
  <div class="max-w-xl mx-auto mt-8">
    <x-flash-message/>
    <h1 class="text-2xl font-bold mb-6 text-center">時間帯タグを編集</h1>

    {{-- 更新フォーム（IDを付ける） --}}
    <form id="updateForm" method="POST" action="{{ route('timing-tags.update', $timing_tag) }}" class="space-y-5">
    @csrf
    @method('PUT')

    {{-- タグ名 --}}
    <div>
        <label class="block font-medium mb-1">タグ名 <span class="text-red-600">*</span></label>
        <input type="text" name="timing_name"
            value="{{ old('timing_name', $timing_tag->timing_name) }}"
            class="w-full border rounded px-3 py-2" required>
        @error('timing_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- 基準時刻 --}}
    <div>
        <label class="block font-medium mb-1">基準時刻 <span class="text-red-600">*</span></label>
        <input type="time" name="base_time"
            value="{{ old('base_time', $timing_tag->base_time ? substr($timing_tag->base_time, 0, 5) : '') }}"
            class="w-full border rounded-lg px-3 py-2" required step="60">
        @error('base_time') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    </form>

    {{-- ボタン列（3つ横並び） --}}
    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('timing-tags.show', $timing_tag) }}"
        class="bg-gray-300 text-gray-800 px-5 py-2 rounded-lg hover:bg-gray-400">
        詳細に戻る
        </a>

    {{-- 更新（外から updateForm を送信） --}}
    <button type="submit" form="updateForm"
            class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">
        更新する
    </button>

    {{-- 削除（独立フォームなので onsubmit が効く） --}}
    <form method="POST" action="{{ route('timing-tags.destroy', $timing_tag) }}"
            onsubmit="return confirm('本当に削除しますか？この操作は元に戻せません。');">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700">
        削除
        </button>
        </form>
    </div>
    </div>
</x-app-layout>
