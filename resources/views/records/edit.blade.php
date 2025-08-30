<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-center">記録の編集</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4">
        {{-- 全体のエラー --}}
        @if( $errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-none">
                    @foreach( $errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <form method="POST" action="{{route('records.update',$record)}}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid sm:grid-cols-2 gap-4">
            {{-- 日付・タイミング --}}
            <div>
                <label class="block text-sm font-medium mb-1">日付</label>
                <input type="date" name="taken_date" value="{{old('taken_date',$record->taken_at->toDateString())}}"
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
            </div>

              {{-- このコンテナに動的に行を追加 --}}
              <div id="medication-container" class="space-y-3">
              </div>

              {{-- JavaScriptのテンプレートとして使うためのHTML --}}
              <template id="medication-template">
                <div class="grid sm:grid-cols-12 gap-3 p-3 border rounded bg-gray-50 medication-row">
                    {{-- 薬の選択 --}}
                    <div class="sm:col-span-6">
                        <label class="block text-xs text-gray-600 mb-1">薬
                            <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full border rounded px-3 py-2" name="medication[0][medication_id]" data-name="medication_id" required>
                            <option value="">選択してください</option>
                                @foreach ($medications as $m)
                                    <option value="{{$m->medication_id}}">
                                        {{ $m->medication_name}}
                                    </option>
                                @endforeach
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

                    {{-- 完了チェック --}}
                    <div class="sm:col-span-2 flex items-end">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" class="rounded" name="medications[0][is_completed]" data-name="is_completed" value="1">
                            <span class="text-sm">完了</span>
                        </label>
                    </div>

                    {{-- 理由のコンテナ --}}
                    <div class="sm:col-span-12 reason-container"></div>

                    {{-- 行削除 --}}
                    <div class="sm:col-span-12 text-right">
                        <button type="button"
                                class="text-sm text-red-600 hover:text-red-800 remove-row-btn">
                            この行を削除
                        </button>
                    </div>
                </div>
            </template>

            <template id="reason-template">
                <div class="mt-3">
                    <label class="block text-xs text-gray-600 mb-1">服用しなかった理由</label>
                    <select name="medications[0][reason_not_taken]" data-name="reason_not_taken" class="w-full border rounded px-3 py-2">
                        <option value="">選択してください</option>
                        <option value="飲み忘れ">飲み忘れ</option>
                        <option value="副作用が心配">副作用が心配</option>
                        <option value="医師の指示">医師の指示</option>
                        <option value="体調不良">体調不良</option>
                        <option value="その他">その他</option>
                    </select>
                </div>
            </template>

            {{-- フィールド単位のエラー表示（ワイルドカード） --}}
            @error('medications')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            @error('medications.*.medication_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            @error('medications.*.taken_dosage')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            @error('medications.*.is_completed')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror

            <div class="pt-2 flex items-center gap-3">
                <button class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">保存する</button>
                <a href="{{route('records.index')}}" class="px-4 py-2 border text-white bg-gray-900 hover:bg-gray-400">キャンセル</a>
            </div>
        </form>
    </div>

    {{-- PHPからJSONに変更したdataをJavaScipt変数として定義 --}}
    <script>
        const existingMedications = @json($record->recordMedications);
    </script>
    {{-- JavaScriptファイルを読み込む --}}
    <script src="{{asset('js/records-edit.js')}}"></script>
</x-app-layout>