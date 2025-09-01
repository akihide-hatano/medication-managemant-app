<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl text-center">内服薬カレンダー</h2>
    </x-slot>

    <div class="container mx-auto p-4 max-w-6xl">
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

        <div id='calendar'></div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ja.js'></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ja',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            // ここにイベントデータを追加します
            events: [
                {
                    title: '内服薬記録',
                    start: '{{ now()->format("Y-m-d") }}' // 今日の日付
                }
            ]
        });
        calendar.render();
    });
    </script>
</x-app-layout>