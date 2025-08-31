<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-serif">内服薬記録一覧</h2>
    </x-slot>

    <div class="container mx-auto p-4 max-w-6xl">
        {{-- 絞り込みフォームをカレンダーピッカーに修正 --}}
        <form action="{{ route('records.index') }}" method="GET" class="mb-6 flex space-x-4 items-end">
            <div>
                <label for="filter_date" class="block text-sm font-medium text-gray-700">日付で絞り込み</label>
                <input type="week" name="filter_date" id="filter_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ request('filter_date') }}">
            </div>

            {{-- 新しい「完了状況」のドロップダウンを追加 --}}
            <div>
                <label for="filter_completion" class="block text-sm font-medium text-gray-700">完了状況</label>
                <select name="filter_completion" id="filter_completion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">すべて</option>
                    <option value="completed" @if(request('filter_completion') === 'completed') selected @endif>完了</option>
                    <option value="incomplete" @if(request('filter_completion') === 'incomplete') selected @endif>未完了</option>
                </select>
            </div>
            
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                絞り込む
            </button>
        </form>

        {{-- 既存のカード表示部分 --}}
        @if ($records->isEmpty())
            <p class="text-red-700">まだ記録はありません。</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($records as $record)
                    @php
                        $total = $record->recordMedications->count();
                        $completed = $record->recordMedications->where('is_completed', true)->count();
                        $allDone = $total > 0 && $total === $completed;
                        $bgClass = $allDone ? 'bg-green-100' : 'bg-red-100';
                    @endphp
                    <a href="{{ route('records.show', $record) }}" class="block h-full">
                        <div class="border rounded-lg p-4 shadow hover:shadow-lg transition {{ $bgClass }} h-full">
                            <h3 class="font-bold text-lg mb-2">
                                {{ $record->taken_at->format('Y-m-d H:i') }}
                                ／ {{ optional($record->timingTag)->timing_name ?? '—' }}
                            </h3>
                            <p class="mb-2">
                                @if ($allDone)
                                    <span class="text-green-600 font-bold">内服完了</span>
                                @else
                                    <span class="text-red-600 font-bold">内服未完了</span>
                                @endif
                            </p>
                            <ul class="mt-2 list-disc pl-6">
                                @forelse ($record->recordMedications as $rm)
                                    @php
                                        $symbol = $rm->is_completed ? '○' : '×';
                                        $color = $rm->is_completed ? 'text-green-600' : 'text-red-600';
                                    @endphp
                                    <li class="flex items-center">
                                        <span class="{{ $color }} font-bold mr-2">{{ $symbol }}</span>
                                        <span>{{ optional($rm->medication)->medication_name ?? '-' }}</span>
                                        @if ($rm->taken_dosage)
                                            <span class="text-gray-500 ml-2">{{ $rm->taken_dosage }}</span>
                                        @endif
                                    </li>
                                @empty
                                    <li class="text-gray-500">
                                        登録された内服薬はありません
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $records->appends(request()->query())->links() }}
            </div>
        @endif
        <div class="mt-6">
            <a class="inline-block bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition"
                href="{{ route('records.create')}}">
                新規作成
            </a>
        </div>
    </div>
</x-app-layout>