<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\TimingTagController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\RecordMedicationController;
use App\Http\Controllers\MedicationReminderController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


     // ===== カレンダー系（具体→一般の順で先に置く） =====

    // カレンダー画面（Blade）
    Route::get('/records/calendar', [RecordController::class, 'calendar'])
        ->name('records.calendar');

    // カレンダーのイベントJSON（FullCalendar等用）
    Route::get('/records/events', [RecordController::class, 'getCalendarEvents'])
        ->name('records.getCalendarEvents');

    // カレンダーから服用完了トグル
    // record_id + medication_id は unique 制約がある前提（衝突なし）
    Route::post('/records/{record}/medications/{medication_id}/toggle-completion',
        [RecordController::class, 'toggleMedicationCompletionFromCalendar'])
        ->whereNumber(['record','medication_id'])
        ->name('records.toggleMedicationCompletionFromCalendar');

    // 通知の既読化
    Route::patch('/medication-reminders/{medicationReminder}/mark-as-read',
        [MedicationReminderController::class, 'markAsRead'])
        ->whereNumber('medicationReminder')
        ->name('medication-reminders.mark-as-read');


    // 薬マスタ
    Route::resource('medications', MedicationController::class);
    // タイミングタグ（URLパラメータ名だけ明示）
    Route::resource('timing-tags', TimingTagController::class)
        ->parameters(['timing-tags' => 'timing_tag']);
    // 記録
    Route::resource('records', RecordController::class);
    // 記録にぶら下がる服薬（ネスト＆shallowで編集/削除URLを浅く）
    Route::resource('records.record-medications', RecordMedicationController::class)
        ->shallow()
        ->parameters([
            'records' => 'record',
            'record-medications' => 'record_medication',
        ]);
});

require __DIR__.'/auth.php';
