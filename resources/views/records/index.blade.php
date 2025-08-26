<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">内服記録一覧</h2>
  </x-slot>

  @if($records->isEmpty())
    <p>まだ記録がありません。</p>
  @else
    <div class="space-y-3">
      @foreach ($records as $record)
        <div class="border rounded p-3">
          <div class="text-sm text-gray-600">
            日付：
            {{ $record->taken_at ? $record->taken_at->format('Y-m-d H:i') : '—' }}
            ／ タイミング：
            {{ $record->timingTag->timing_name ?? '—' }}
          </div>

          @php($count = $record->recordMedications->count())
          <ul class="list-disc pl-6 text-sm mt-1">
            @foreach ($record->recordMedications->take(3) as $rm)
              <li>{{ $rm->medication->medication_name ?? '—' }}</li>
            @endforeach
            @if($count > 3)
              <li>…ほか {{ $count - 3 }} 件</li>
            @endif
          </ul>

          <div class="mt-2">
            <a class="text-blue-600 underline" href="{{ route('records.show', $record) }}">詳細を見る</a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">{{ $records->links() }}</div>
  @endif

  <div class="mt-6">
    <a class="inline-block bg-blue-600 text-white px-4 py-2 rounded" href="{{ route('records.create') }}">新規作成</a>
  </div>
</x-app-layout>
