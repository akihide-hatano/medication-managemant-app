import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new Calendar(calendarEl, {
        plugins: [ dayGridPlugin ],
        initialView: 'dayGridMonth',
        locale: 'ja',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
        },
        // APIからイベントデータを取得するように設定
        events: {
            url: '/api/records/events', // ここを新しいAPIルートに修正
            method: 'GET',
            failure: function() {
                alert('内服薬記録の取得中にエラーが発生しました。');
            }
        }
    });
    calendar.render();
});