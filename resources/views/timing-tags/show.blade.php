<x-app-layout>
    <div class="max-w-xl mx-auto mt-8">
        <x-flash-message />

        <h1 class="text-2xl font-bold mb-6 text-center">時間帯タグ詳細</h1>

        <div class="bg-white shadow rounded-lg p-6 border">
            <p class="mb-4">
                <span class="font-semibold text-gray-700">ID：</span>
                <span class="text-gray-800">{{ $timing_tag->timing_tag_id }}</span>
            </p>
            <p class="mb-4">
                <span class="font-semibold text-gray-700">タグ名：</span>
                <span class="text-gray-800">{{ $timing_tag->timing_name }}</span>
            </p>
            <p class="mb-6">
                <span class="font-semibold text-gray-700">基準時刻：</span>
                <span class="text-gray-800">{{ $timing_tag->base_time ? substr($timing_tag->base_time,0,5) : '—' }}</span>
            </p>

            <div class="flex justify-between">
                <a href="{{ route('timing-tags.index') }}"
                   class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">
                    一覧へ戻る
                </a>
                {{-- <div class="flex gap-2">
                    <a href="{{ route('timing_tags.edit', $timing_tag) }}"
                       class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        編集
                    </a>
                    <form action="{{ route('timing_tags.destroy', $timing_tag) }}"
                          method="POST"
                          onsubmit="return confirm('削除しますか？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            削除
                        </button>
                    </form>
                </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>
