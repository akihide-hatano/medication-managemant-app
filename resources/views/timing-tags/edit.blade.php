<x-app-layout>
    <div class="max-w-xl mx-auto mt-8">
        <x-flash-message/>
        <h1 class="text-2xl font-bold mb-6 text-center">時間帯タグを編集</h1>

        <form method="POST" action="{{ route('timing-tags.update',$timing_tag)}}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- タグ名 --}}
            <div>
                <label class="block font-medium mb-1">タグ名
                    <span class="text-red-600">*</span>
                </label>
                <input type="text" name="timing_name" value="{{old('timing_name',$timing_tag->timing_name)}}"
                class="w-full border rounded px-3 py-2 required">

                @error('timing_name')
                <p class="text-red-600 text-sm mt-1">{{ $message}} </p>
                @enderror
            </div>

            {{-- 基準時間 --}}
            <div>
                <label class="block font-medium mb-1">基準時刻
                    <span class="text-red-600">*</span>
                </label>
                <input type="time" name="base_time" value="{{ old('base_time',$timing_tag->base_time ? substr($timing_tag->base_time, 0, 5) : '')}}
                " class="w-full border rounded-lg px-3 py-2 "required step="60">
                @error('base_time')
                <p class="text-red-600 text-sm mt-1">{{ $message}}</p>
                @enderror
            </div>

        </form>
    </div>
</x-app-layout>