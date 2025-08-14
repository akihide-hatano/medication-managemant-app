<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class RecordsTableSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('user_id')->all();            // users主キーが user_id 前提
        $tags    = DB::table('timing_tags')->get(['timing_tag_id','base_time']);

        if (empty($userIds) || $tags->isEmpty()) {
            $this->command->warn('Users or timing_tags are empty. Seed them first.');
            return;
        }

        $rows = [];
        // 直近7日間のどこか＋タグのbase_timeで30件作成
        for ($i = 0; $i < 30; $i++) {
            $uid = Arr::random($userIds);
            $tag = $tags->random();

            // 直近7日の乱数日
            $dayOffset = random_int(0, 6);
            $date = Carbon::now()->subDays($dayOffset);

            // base_timeがあればその時間、無ければ現在時刻
            $takenAt = $tag->base_time
                ? Carbon::parse($date->toDateString() . ' ' . $tag->base_time)
                : Carbon::now();

            $rows[] = [
                'user_id'       => $uid,
                'timing_tag_id' => $tag->timing_tag_id,
                'taken_at'      => $takenAt,
                'created_at'    => $takenAt,
                'updated_at'    => $takenAt,
            ];
        }

        DB::table('records')->insert($rows);
    }
}
