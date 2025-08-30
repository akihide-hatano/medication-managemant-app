<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-center">記録の編集</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4">
        {{-- 全体のエラー --}}
        @if( $errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class=""list-none>
                    @foreach( $errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('records.update',$record)}}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid sm:grid-cols-2 gap-4">
            {{-- 日付・タイミング --}}
            <div>
                <label class="block text-sm font-medium mb-1">日付</label>
                <input type="date" name="taken_date" value="{{ old('taken_date',$record->taken_at->toDateString() )}}"
                    class="w-full border rounded px-3 py-2 ">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">タイミング</label>
                <select name="timing_tag_id" class="w-full border rounded px-3 py-2">
                    @foreach( $timingTags as $t)
                        <option value=" {{ $t->timing_tag_id}}" @selected(old('timing_tag_id', $record->timing_tag_id) == $t->timing_tag_id)>
                            {{ $t->timing_name}}
                        </option>
                    @endforeach
                </select>
                    @error('timimg_tag_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex flex-grow justify-center">
                    <h3 class="font-semibold">内服薬</h3>
                </div>
                <button type="button"
                    id="add-medication-row"
                    class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                行を追加
              </button>

              {{-- このコンテナに動的に行を追加 --}}
              <div id="medication-container" class="space-y-3">
              </div>

              {{-- JavaScriptのテンプレートとして使うためのHTML --}}
              <template id="medication-templete">
                <div class="grid sm:grid-cols-12 gap-3 p-3 border rounded bg-gray-50 medication-row">
                    {{-- 薬の選択 --}}
                    <div class="sm:col-span-6">
                        <label class="block text-xs text-gray-600 mb-1">薬
                            <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full borer rounded px-3 py-2" name="medication[0][medication_id]" data-name="medication-id" required>
                            <option value="">選択してください
                                @foreach ($medications as $m)
                                    <option value="{{$m->medication_id}}">
                                        {{ $m->medication_name}}
                                    </option>
                                @endforeach
                            </option>
                        </select>
                    </div>

                    {{-- 内服量の選択 --}}
                    <div class="sm:col-span-4">
                        <label class="block text-xs text-gray-600 mb-1">服用量</label>
                        <select name="medications[0][taken_dosage]" class="w-full border rounded px-3 py-2">
                            <option value="">未設定</option>
                            <option value="1錠">1錠</option>
                            <option value="2錠">2錠</option>
                            <option value="3錠">3錠</option>
                            <option value="4錠">4錠</option>
                            <option value="5錠">5錠</option>
                        </select>
                    </div>
              </template>
          </div>
    </form>
</x-app-layout>