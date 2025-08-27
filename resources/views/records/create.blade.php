<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">内服記録を追加（まとめて登録）</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4 space-y-6">
        <form method="POST" action="{{ route('records.store') }}" class="space-y-6">
        @csrf
    <div>
        <label class="block text-sm font-medium">日付</label>
        <input type="date" name="taken_date"
                value="{{ old('taken_date', now()->toDateString()) }}"
                class="mt-1 w-full border rounded px-3 py-2" required>
        @error('taken_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">タイミング</label>
        <select name="timing_tag_id" class="mt-1 w-full border rounded px-3 py-2" required>
            <option value="">選択してください</option>
            @foreach($timingTags as $tag)
            <option value="{{ $tag->timing_tag_id }}" @selected(old('timing_tag_id')==$tag->timing_tag_id)>
                {{ $tag->timing_name }}
                @if($tag->base_time)（{{ \Carbon\Carbon::parse($tag->base_time)->format('H:i') }}）@endif
            </option>
            @endforeach
        </select>
        @error('timing_tag_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

      <div>
        <label class="block text-sm font-medium mb-2">内服薬（複数選択 可）</label>

        <div class="space-y-2">
          @foreach($medications as $m)
            <div class="flex items-center gap-3 border rounded p-2">
              <label class="inline-flex items-center gap-2 w-56">
                <input type="checkbox" name="medications[{{ $loop->index }}][id]" value="{{ $m->medication_id }}">
                <span>{{ $m->medication_name }}</span>
              </label>

              <input type="text" name="medications[{{ $loop->index }}][dosage]"
                     class="border rounded px-2 py-1 w-40" placeholder="用量(任意)"
                     value="{{ old("medications.$loop->index.dosage") }}">

              <label class="inline-flex items-center gap-1">
                <input type="checkbox" name="medications[{{ $loop->index }}][done]" value="1">
                <span>完了</span>
              </label>
            </div>
          @endforeach
        </div>

        @error('medications') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        @foreach ($errors->get('medications.*.id') as $msgs)
          @foreach ($msgs as $msg)
            <p class="text-red-600 text-sm mt-1">{{ $msg }}</p>
          @endforeach
        @endforeach
      </div>

      <div class="flex items-center gap-3">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
          登録する
        </button>
        <a href="{{ route('records.index') }}" class="text-gray-600 underline">一覧に戻る</a>
      </div>
    </form>
  </div>
</x-app-layout>
