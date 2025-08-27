    <x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">内服記録一覧</h2>
    </x-slot>

        @if($records->isEmpty())
            <p>まだ記録がありません。</p>
        @else
            <div class="space-y-3">
        @foreach($records as $record)
            @php
                $total     = $record->recordMedications->count();
                $completed = $record->recordMedications->where('is_completed', true)->count();
                $allDone   = $total > 0 && $total === $completed; // 全部○か？
                $bgClass   = $allDone ? 'bg-green-100' : 'bg-red-100';
            @endphp

            <div class="border rounded p-4 mb-4 {{ $bgClass }}">
                <h3 class="font-bold text-lg">
                    {{ $record->taken_at->format('Y-m-d H:i') }}
                    ／ {{ optional($record->timingTag)->timing_name ?? '—' }}
                    @if($allDone)
                        <span class="text-green-600 font-bold ml-2">（全服用完了）</span>
                    @else
                        <span class="text-red-600 font-bold ml-2">（未完了）</span>
                    @endif
                </h3>

                <ul class="mt-2 list-disc pl-6">
                    @forelse($record->recordMedications as $rm)
                        @php
                            $symbol = $rm->is_completed ? '○' : '×';
                            $color  = $rm->is_completed ? 'text-green-600' : 'text-red-600';
                        @endphp
                        <li>
                            <span class="{{ $color }} font-bold">{{ $symbol }}</span>
                            {{ optional($rm->medication)->medication_name ?? '—' }}
                            @if($rm->taken_dosage)
                                <span class="text-gray-500">（{{ $rm->taken_dosage }}）</span>
                            @endif
                        </li>
                    @empty
                        <li class="text-gray-500">登録された内服薬はありません</li>
                    @endforelse
                </ul>
            </div>
        @endforeach

    </div>

    <div class="mt-4">{{ $records->links() }}</div>
  @endif

    <div class="mt-6">
        <a class="inline-block bg-blue-600 text-white px-4 py-2 rounded" href="{{ route('records.create') }}">新規作成</a>
    </div>
</x-app-layout>
