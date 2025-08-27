<x-app-layout>
  <x-slot name="header"><h2 class="text-xl font-semibold">記録の新規作成</h2></x-slot>

  <div class="max-w-3xl mx-auto p-4" x-data="medForm()">
    <form method="POST" action="{{ route('records.store') }}" class="space-y-6">
      @csrf

      {{-- 日付・タイミング --}}
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">日付</label>
          <input type="date" name="taken_date" value="{{ old('taken_date', now()->toDateString()) }}"
                 class="w-full border rounded px-3 py-2">
          @error('taken_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">タイミング</label>
          <select name="timing_tag_id" class="w-full border rounded px-3 py-2">
            @foreach($timingTags as $t)
              <option value="{{ $t->timing_tag_id }}" @selected(old('timing_tag_id')==$t->timing_tag_id)>
                {{ $t->timing_name }}
              </option>
            @endforeach
          </select>
          @error('timing_tag_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      {{-- 薬を一括選択（複数） --}}
      <div>
        <label class="block text-sm font-medium mb-1">内服薬（複数選択可）</label>
        <select name="medications[]" multiple size="8"
                class="w-full border rounded px-3 py-2"
                x-model="selected">
          @foreach($medications as $m)
            <option value="{{ $m->medication_id }}">
              {{ $m->medication_name }}
            </option>
          @endforeach
        </select>
        @error('medications')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        @error('medications.*')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        <p class="text-xs text-gray-500 mt-1">Ctrl/⌘ を押しながらクリックで複数選択できます。</p>
      </div>

      {{-- 選んだ薬だけ詳細入力（用量／完了） --}}
      <template x-if="selected.length">
        <div class="border rounded p-4 space-y-3">
          <h3 class="font-semibold">選択した薬の詳細</h3>

          <template x-for="mid in selected" :key="mid">
            <div class="grid sm:grid-cols-12 items-center gap-3">
              <div class="sm:col-span-5">
                <span class="font-medium" x-text="medName(mid)"></span>
              </div>
              <div class="sm:col-span-5">
                <input class="w-full border rounded px-3 py-2"
                       :name="`dosages[${mid}]`" placeholder="用量（任意）">
              </div>
              <label class="sm:col-span-2 inline-flex items-center gap-2">
                <input type="checkbox" :name="`done[${mid}]`" value="1">
                <span>服用完了</span>
              </label>
            </div>
          </template>
        </div>
      </template>

      <div class="pt-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">保存する</button>
      </div>
    </form>
  </div>

  {{-- Alpine 初期化 --}}
  <script>
    function medForm() {
      const names = {
        @foreach($medications as $m)
          {{ $m->medication_id }}: @json($m->medication_name),
        @endforeach
      };
      return {
        selected: @json(old('medications', [])),
        medName(id){ return names[id] ?? `#${id}`; },
      };
    }
  </script>
</x-app-layout>
