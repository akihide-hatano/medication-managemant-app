<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h1 class="text-3xl font-bold mb-6 text-center">内服薬詳細</h1>

                    {{-- 薬の情報が取得できた場合 --}}
                    @if ($medication)
                        {{-- カードコンポーネントの開始 --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($medication as $md)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6">
                                    <h2 class="text-xl font-bold mb-2 text-indigo-700">{{ $md->medication_name }}</h2>
                                    <hr class="mb-4">
                                    <div class="space-y-3">
                                        <p>
                                            <span class="font-semibold text-gray-700">処方量:</span>
                                            <span class="text-gray-600">{{ $md->dosage }}</span>
                                        </p>
                                        <p>
                                            <span class="font-semibold text-gray-700">作用:</span>
                                            <span class="text-gray-600">{{ $md->effects }}</span>
                                        </p>
                                        <p>
                                            <span class="font-semibold text-gray-700">副作用:</span>
                                            <span class="text-gray-600">{{ $md->side_effects }}</span>
                                        </p>
                                        <p>
                                            <span class="font-semibold text-gray-700">用途:</span>
                                            <span class="text-gray-600">{{ $md->notes }}</span>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- 薬の情報がない場合のメッセージ --}}
                        <div class="text-center text-gray-500">
                            内服薬が見つかりませんでした。
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>