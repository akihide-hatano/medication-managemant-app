<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-serif">内服薬記録一覧</h2>
    </x-slot>

    <div class="max-w-screen-lg mx-auto p-4">
        @if ($records->isEmpty())
            <p class="text-red-700">まだ記録はありません。</p>
        @else
            
        @endif
    </div>



</x-app-layout>