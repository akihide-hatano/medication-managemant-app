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

        // ← ここを IDs の素配列にする（型ブレ対策）
        $medIds = DB::table('medications')->pluck('medication_id')->all();

        if ($records->isEmpty() || empty($medIds)) {
            $this->command->warn('records か medications が空です。先にシードしてください。');
            return;
        }

        $reasons = ['飲み忘れ','体調不良で中止','医師指示で休薬','外出中で服薬不可','副作用懸念'];
        $rows = [];

        foreach ($records as $rec) {
            $pickCount = random_int(1, min(3, count($medIds)));

            // Arr::random は個数=1だとスカラーを返す → (array) で常に配列化
            $pickedIds = (array) Arr::random($medIds, $pickCount);

            foreach ($pickedIds as $mid) {
                // 90% true（= 内服完了）
                $completed = random_int(1, 100) <= 90;

                $rows[] = [
                    'record_id'        => $rec->record_id,
                    'medication_id'    => $mid,
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
