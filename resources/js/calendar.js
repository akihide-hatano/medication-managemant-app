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
        },
        
        //eventが並びかえること
        eventOrder: ['sort_order', 'title'],
        eventOrderStrict: true,

        eventClick: function(info) {
            // クリックされたイベントの日付を取得
            const dateStr = info.event.startStr;
        
            // 該当日の内服記録詳細ページへ遷移
            // 遷移先のURLは、records.showルートに対応させます
            window.location.href = `/records/${dateStr}`;
        }
    });
    calendar.render();
});