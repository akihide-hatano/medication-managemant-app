<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">記録の新規作成</h2>
  </x-slot>

  <div class="max-w-3xl mx-auto p-4"
       x-data="medForm()">
    {{-- 全体のエラー --}}
    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('records.store') }}" class="space-y-6">
      @csrf

      {{-- 日付・タイミング --}}
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">日付</label>
          <input type="date" name="taken_date"
                 value="{{ old('taken_date', now()->toDateString()) }}"
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

      {{-- 内服薬行（1行＝1内服薬） --}}
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <h3 class="font-semibold">内服薬</h3>
          <button type="button"
                  class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
                  @click="addRow()">
            行を追加
          </button>
        </div>

        <template x-for="(row, i) in meds" :key="i">
          <div class="grid sm:grid-cols-12 gap-3 p-3 border rounded bg-gray-50">
            {{-- 薬の選択 --}}
            <div class="sm:col-span-6">
              <label class="block text-xs text-gray-600 mb-1">薬 <span class="text-red-500">*</span></label>
              <select class="w-full border rounded px-3 py-2"
                      x-model="row.medication_id"
                      :name="`medications[${i}][medication_id]`"
                      required>
                <option value="">選択してください</option>
                @foreach($medications as $m)
                  <option value="{{ $m->medication_id }}">{{ $m->medication_name }}</option>
                @endforeach
              </select>
            </div>

            {{-- 服用量 --}}
            <div class="sm:col-span-4">
              <label class="block text-xs text-gray-600 mb-1">服用量（任意）</label>
              <select class="w-full border rounded px-3 py-2"
                      x-model="row.taken_dosage"
                      :name="`medications[${i}][taken_dosage]`">
                <option value="">未指定</option>
                <option value="1錠">1 錠</option>
                <option value="2錠">2 錠</option>
                <option value="3錠">3 錠</option>
                <option value="4錠">4 錠</option>
                <option value="5錠">5 錠</option>
              </select>
            </div>

            {{-- 完了チェック --}}
            <div class="sm:col-span-2 flex items-end">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox"
                       class="rounded"
                       x-model="row.is_completed"
                       :name="`medications[${i}][is_completed]`"
                       value="1">
                <span class="text-sm">完了</span>
              </label>
            </div>

            {{-- 行削除 --}}
            <div class="sm:col-span-12 text-right">
              <button type="button"
                      class="text-sm text-red-600 hover:text-red-800"
                      @click="removeRow(i)"
                      x-show="meds.length > 1">
                この行を削除
              </button>
            </div>
          </div>
        </template>

        {{-- フィールド単位のエラー表示（ワイルドカード） --}}
        @error('medications')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        @error('medications.*.medication_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        @error('medications.*.taken_dosage')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        @error('medications.*.is_completed')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div class="pt-2 flex items-center gap-3">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">保存する</button>
        <a href="{{ route('records.index') }}"
           class="px-4 py-2 rounded border hover:bg-gray-50">キャンセル</a>
      </div>
    </form>
  </div>

  <script>
    function medForm () {
      // old() から初期行を復元（なければ1行だけ空で作成）
      const old = @json(old('medications', []));
      const normalize = (r) => ({
        medication_id: r?.medication_id ?? '',
        taken_dosage : r?.taken_dosage ?? '',
        // チェックボックスは '1' が来ていれば true
        is_completed : !!(r?.is_completed)
      });

      const initial = old.length ? old.map(normalize) : [normalize({})];

      return {
        meds: initial,
        addRow(){
          this.meds.push(normalize({}));
        },
        removeRow(i){
          this.meds.splice(i, 1);
        }
      }
    }
  </script>
</x-app-layout>
