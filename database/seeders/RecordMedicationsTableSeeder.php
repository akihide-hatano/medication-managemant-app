<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class RecordMedicationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $records = DB::table('records')->get(['record_id','taken_at']);
        $meds    = DB::table('medications')->get(['medication_id','dosage']); // ← Collection（要素は stdClass）

        if ($records->isEmpty() || $meds->isEmpty()) {
            $this->command->warn('records か medications が空です。先にシードしてください。');
            return;
        }

        $reasons = ['飲み忘れ','体調不良で中止','医師指示で休薬','外出中で服薬不可','副作用懸念'];
        $rows = [];

        foreach ($records as $rec) {
            $pickCount = random_int(1, min(3, $meds->count()));

            // ★ 常に Collection：シャッフルして先頭 n 件を取る
            $picked = $meds->shuffle()->take($pickCount);

            foreach ($picked as $m) {
                // 90% 完了（true）
                $completed = random_int(1, 100) <= 90;

                $rows[] = [
                    'record_id'        => $rec->record_id,
                    'medication_id'    => $m->medication_id,        // ← stdClass のプロパティ
                    'taken_dosage'     => random_int(1, 5) . '錠',
                    'is_completed'     => $completed,
                    'reason_not_taken' => $completed ? null : Arr::random($reasons),
                    'created_at'       => $rec->taken_at,
                    'updated_at'       => $rec->taken_at,
                ];
            }
        }

        DB::table('record_medications')->upsert(
            $rows,
            ['record_id','medication_id'],
            ['taken_dosage','is_completed','reason_not_taken','updated_at']
        );
    }
}
