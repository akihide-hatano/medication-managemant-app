{{-- resources/views/medications/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            薬一覧
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- フラッシュメッセージ --}}
            <x-flash-message />

            {{-- 検索フォーム --}}
            <form method="GET" action="{{ route('medications.index') }}" class="mb-4 flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}"
                    placeholder="薬名で検索"
                    class="flex-1 rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">検索</button>
            </form>

            {{-- 一覧カード --}}
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                @forelse($medications as $medication)
                    <a href="{{ route('medications.show', $medication) }}"
                    class="block rounded-lg border bg-white p-4 shadow transition hover:-translate-y-1 hover:shadow-lg hover:bg-blue-200">
                        <h3 class="mb-2 text-lg font-bold text-gray-800">{{ $medication->medication_name }}</h3>
                        <p class="text-sm text-gray-500">用量: {{ $medication->dosage ?? '—' }}</p>
                        <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                            {{ Str::limit($medication->effects, 40, '…') ?? '—' }}
                        </p>
                    </a>
                @empty
                    <p class="col-span-full text-gray-500">薬が登録されていません。</p>
                @endforelse
            </div>
            {{-- ページネーション --}}
            <div class="mt-6">
                {{ $medications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
