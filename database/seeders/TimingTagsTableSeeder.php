<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimingTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $rows = [
            // sort_order, timing_name,     base_time
            [1, '朝食前',  '07:00:00'],
            [2, '朝食後',  '08:00:00'],
            [3, '昼食前',  '12:00:00'],
            [4, '昼食後',  '13:00:00'],
            [5, '夕食前',  '18:00:00'],
            [6, '夕食後',  '19:00:00'],
            [7, '就寝前',  '22:00:00'],
            [8, '頓用',    null],
        ];

        foreach ($rows as [$order, $name, $time]) {
            DB::table('timing_tags')->insert([
                'timing_name' => $name,
                'base_time'   => $time,
                'sort_order'  => $order,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}