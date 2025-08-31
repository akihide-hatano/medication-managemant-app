<x-app-layout>
    <x-slot name="header">
        <div class="px-4 sm:px-6 lg:px-8 py-4 bg-gray-100 border-b border-gray-200">
            <h2 class="text-2xl font-semibold leading-tight text-gray-800">
                記録詳細
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-xl sm:rounded-lg">
                <div class="flex items-center justify-between">
                    {{-- 日付とタイミングを表示 --}}
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $record->taken_at->format('Y年m月d日') }}</h1>
                    </div>
                    {{-- 編集ボタン --}}
                    <a href="{{ route('records.edit', $record) }}"
                       class="rounded-md bg-blue-600 px-6 py-2 text-white font-bold hover:bg-blue-700 shadow-lg transition-colors">編集</a>
                </div>

                <hr class="my-6 border-t-2 border-gray-200">

                {{-- 内服薬のリスト --}}
                <div class="space-y-6">
                    <h3 class="text-xl font-semibold text-gray-700">内服薬 <span class="text-sm font-normal text-gray-500">({{ $record->timingTag->timing_name }})</span></h3>
                    @forelse($record->recordMedications as $recordMedication)
                        {{-- 完了と未完了で背景色を変更 --}}
                        <div class="p-5 rounded-xl shadow-lg
                            @if($recordMedication->is_completed)
                                bg-green-50
                            @else
                                bg-red-50
                            @endif
                        ">
                            <h4 class="font-bold text-gray-800 mb-2 flex items-center">
                                @if($recordMedication->is_completed)
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                @endif
                                {{ $recordMedication->medication->medication_name }}
                            </h4>
                            <p class="text-sm text-gray-600 mb-1"><strong>服用量:</strong> {{ $recordMedication->taken_dosage ?? '未設定' }}</p>
                            <p class="text-sm">
                                <strong>内服状況:</strong>
                                <span class="font-bold
                                    @if($recordMedication->is_completed)
                                        text-green-600
                                    @else
                                        text-red-600
                                    @endif
                                ">
                                    @if($recordMedication->is_completed)
                                        完了
                                    @else
                                        未完了
                                    @endif
                                </span>
                            </p>
                            @if(!$recordMedication->is_completed && $recordMedication->reason_not_taken)
                                <p class="text-sm text-gray-600 mt-1"><strong>理由:</strong> {{ $recordMedication->reason_not_taken }}</p>
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