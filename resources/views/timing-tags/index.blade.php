<x-app-layout>
    <div class="max-w-3xl mx-auto mt-8">
        <x-flash-message />
        <h1 class="text-2xl font-bold mb-4">時間帯タグ一覧</h1>

        @if($timing_tags->isEmpty())
            <p class="text-gray-500">タグはありません。</p>
        @else
            <table class="w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border">ID</th>
                        <th class="px-3 py-2 border">タグ名</th>
                        <th class="px-3 py-2 border">基準時刻</th>
                        <th class="px-3 py-2 border w-40">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timing_tags as $tag)
                        <tr>
                            <td class="px-3 py-2 border">{{ $tag->timing_tag_id }}</td>
                            <td class="px-3 py-2 border">{{ $tag->timing_name }}</td>
                            <td class="px-3 py-2 border">
                                {{ $tag->base_time ? substr($tag->base_time, 0, 5) : '—' }}
                            </td>
                            <td class="px-3 py-2 border text-center space-x-2">
                                {{-- 編集ボタン --}}
                                <a href="{{ route('timing-tags.edit', $tag) }}"
                                    class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                                    編集
                                </a>

                                {{-- 削除ボタン --}}
                                <form action="{{ route('timing-tags.destroy', $tag) }}"
                                    method="POST"
                                    onsubmit="return confirm('削除しますか？')"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-red-600 rounded hover:bg-red-700">
                                        削除
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
