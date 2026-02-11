<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaderboardSeeder extends Seeder
{
    public function run(): void
    {
        // Dummy Wordle top scores
        $now = now();
        DB::table('leaderboard_scores')->insert([
            ['user_id' => null, 'name' => 'bob', 'game' => 'wordle', 'score' => 6000, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'john', 'game' => 'wordle', 'score' => 5500, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'coolgamer420', 'game' => 'wordle', 'score' => 5000, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'crazyguy69', 'game' => 'wordle', 'score' => 4500, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'funnynumber1', 'game' => 'wordle', 'score' => 4000, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'cool_guy', 'game' => 'wordle', 'score' => 3500, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'gangsterGamer', 'game' => 'wordle', 'score' => 3000, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'knowledge-knower', 'game' => 'wordle', 'score' => 2500, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'smart_person', 'game' => 'wordle', 'score' => 2000, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'word-knower', 'game' => 'wordle', 'score' => 1500, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Dummy Muziek top scores
        DB::table('leaderboard_scores')->insert([
            ['user_id' => null, 'name' => 'bob', 'game' => 'muziek', 'score' => 5000, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'coolgamer420', 'game' => 'muziek', 'score' => 4800, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'musicLover67', 'game' => 'muziek', 'score' => 4600, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'funny-song-guy', 'game' => 'muziek', 'score' => 4400, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'anonymous', 'game' => 'muziek', 'score' => 4200, 'created_at' => $now, 'updated_at' => $now],
        ]);
        // Dummy RPS top scores
        DB::table('leaderboard_scores')->insert([
            ['user_id' => null, 'name' => 'bob', 'game' => 'rps', 'score' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'john', 'game' => 'rps', 'score' => 9, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'coolgamer420', 'game' => 'rps', 'score' => 8, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'crazyguy69', 'game' => 'rps', 'score' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => null, 'name' => 'funnynumber1', 'game' => 'rps', 'score' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
