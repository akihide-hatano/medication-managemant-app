<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-serif">内服薬記録一覧</h2>
    </x-slot>

    <div class="max-w-screen-lg mx-auto p-4">
        @if ($records->isEmpty())
            <p class="text-red-700">まだ記録はありません。</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($records as $record)
                    @php
                        $total = $record->recordMedications->count();
                        $completed = $record->recordMedications->where('is_completed',true)->count();
                        $allDone = $total >0 && $total === $completed;
                        if($allDone){
                            $bgClass = 'bg-green-100';
                        }
                        else{
                            $bgClass = 'bg-red-100';
                        }
                    @endphp

                    <div class="border rounded-lg p-4 shadow hover:shadow-lg transition {{$bgClass}}">
                        <h3 class="font-bold text-lg mb-2">
                            {{$record->taken_at->format('Y-m-d H:i') }}
                            ／ {{ optional($record->timingTag)->timing_name ?? '—' }}
                        </h3>

                        <p class="mb-2">
                            @if ($allDone)
                                <span class="text-green-600 font-bold"> 内服完了</span>
                            @else
                                <span class="text-red-600 font-bold">内服未完了</span>
                            @endif
                        </p>

                        <ul class="mt-2 list-disc pl-6">
                            @foreach ($record->recordMedications as $rm)
                            @php
                                $symbol = $rm->is_completed ? '○' : '×';
                                $color = $rm->is_completed ? 'text-green-600' :'text-red-600';
                             @endphp
                                <li class="flex items-center">
                                    <span class=" {{color}} font-bold mr-2">{{$symbol}}</span>
                                    <span>{{ optional($rm->medication)->medication_name ?? '-'}}</span>
                                    @if($rm->taken_dosage)
                                    <span class="text-gray-500">{{$rm->taken_dosage}}</span>
                                    @endif
                                </li>
                            @empty($rm)
                                <li class="text-gray-500">
                                    登録された内服薬はありません
                                </li>
                            @endempty
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="mt-6">
            <a class="inline-block bg-blue-600 text-white px-4 py-2 rounded shodow hover:bg-blue-700 transition"
                href="{{ route('records.create')}}">
                新規作成
            </a>
        </div>
    </div>
</x-app-layout>