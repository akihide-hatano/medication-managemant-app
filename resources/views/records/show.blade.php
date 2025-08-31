<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            記録詳細
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between">
                    {{-- 日付とタイミングを表示 --}}
                    <div>
                        <h1 class="text-2xl font-bold">{{ $record->taken_at->format('Y年m月d日') }}</h1>
                    </div>
                    {{-- 編集ボタン --}}
                    <a href="{{ route('records.edit', $record) }}"
                       class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">編集</a>
                </div>

                <hr class="my-4">

                {{-- 内服薬のリスト --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">内服薬</h3>
                    <p class="text-gray-600">{{ $record->timingTag->timing_name }}</p>
                    @forelse($record->recordMedications as $recordMedication)
                        <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                            <h4 class="text-xl font-bold text-blue-800 mb-2">{{ $recordMedication->medication->medication_name }}</h4>
                            <p class="text-sm pr-2 text-gray-600"><strong>服用量</strong> {{ $recordMedication->taken_dosage ?? '未設定' }}</p>
                            <p class="text-sm pr-2 text-gray-600"><strong>内服状況</strong>
                                @if($recordMedication->is_completed)
                                    <span class="text-green-600">完了</span>
                                @else
                                    <span class="text-red-600">未完了</span>
                                @endif
                            </p>
                            @if(!$recordMedication->is_completed && $recordMedication->reason_not_taken)
                                <p class="text-sm text-gray-600"><strong>理由</strong> {{ $recordMedication->reason_not_taken }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">内服薬の記録はありません。</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>