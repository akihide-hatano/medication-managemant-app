<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h1 class="text-3xl font-bold mb-6 text-center">内服薬詳細</h1>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold mb-2 text-indigo-700">{{ $medication->medication_name }}</h2>
                        <hr class="mb-4">
                        <div class="space-y-3">
                            <p><span class="font-semibold text-gray-700">処方量:</span>
                               <span class="text-gray-600">{{ $medication->dosage }}</span>
                            </p>
                            <p><span class="font-semibold text-gray-700">作用:</span>
                               <span class="text-gray-600">{{ $medication->effects }}</span>
                            </p>
                            <p><span class="font-semibold text-gray-700">副作用:</span>
                               <span class="text-gray-600">{{ $medication->side_effects }}</span>
                            </p>
                            <p><span class="font-semibold text-gray-700">用途:</span>
                               <span class="text-gray-600">{{ $medication->notes }}</span>
                            </p>
                        </div>
                        
                    <div class="flex items-end justify-end gap-2">
                         {{-- 一覧へ --}}
                            <a href="{{ route('medications.index') }}"
                                class="inline-flex items-center rounded-lg bg-green-600 border px-4 py-2 text-sm font-semibold text-white hover:bg-green-500">
                                一覧へ
                            </a>
                        {{-- 編集 --}}
                            <a href="{{ route('medications.edit', $medication) }}"
                                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                編集
                            </a>
                        {{-- 削除 --}}
                            <form action="{{ route('medications.destroy', $medication) }}" method="POST"
                                    onsubmit="return confirm('本当に削除しますか？この操作は元に戻せません。');">
                                    @csrf
                                    @method('DELETE')
                                <button type="submit"
                                class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                削除
                                </button>
                            </form>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
