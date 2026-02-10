<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('games')->insert([
            ['name' => 'Wordle', 'slug' => 'wordle', 'route' => 'wordle', 'description' => 'Het leuke woordenraadspel', 'created_at' => now(), 'updated_at' => now() , 'working' => TRUE],
            ['name' => 'Muziek', 'slug' => 'muziek', 'route' => 'muziek', 'description' => 'Luister en raden', 'created_at' => now(), 'updated_at' => now(), 'working' => FALSE],
            ['name' => 'rock paper scissors', 'slug' => 'rock-paper-scissors', 'route' => 'rps', 'description' => 'Het klassieke steen-papier-schaar spel', 'created_at' => now(), 'updated_at' => now(), 'working' => TRUE],
        ]);
    }
}
