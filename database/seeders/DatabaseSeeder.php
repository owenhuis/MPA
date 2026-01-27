<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Seed wordle table
        DB::table('wordle')->insert([
    ['woord' => 'apple', 'description' => 'A fruit', 'lengte' => 5],
    ['woord' => 'grape', 'description' => 'A fruit', 'lengte' => 5],
    ['woord' => 'peach', 'description' => 'A fruit', 'lengte' => 5],
    ['woord' => 'berry', 'description' => 'A fruit', 'lengte' => 5],
    ['woord' => 'lemon', 'description' => 'A fruit', 'lengte' => 5],
    ['woord' => 'mango', 'description' => 'A fruit', 'lengte' => 5],
    ['woord' => 'melon', 'description' => 'A fruit', 'lengte' => 5],
    ['woord' => 'olive', 'description' => 'A fruit', 'lengte' => 5],
    ['woord' => 'guava', 'description' => 'A fruit', 'lengte' => 5],
    ['woord' => 'papaw', 'description' => 'A fruit', 'lengte' => 5],

    ['woord' => 'chair', 'description' => 'An object', 'lengte' => 5],
    ['woord' => 'table', 'description' => 'An object', 'lengte' => 5],
    ['woord' => 'couch', 'description' => 'An object', 'lengte' => 5],
    ['woord' => 'shelf', 'description' => 'An object', 'lengte' => 5],
    ['woord' => 'clock', 'description' => 'An object', 'lengte' => 5],
    ['woord' => 'light', 'description' => 'An object', 'lengte' => 5],
    ['woord' => 'phone', 'description' => 'An object', 'lengte' => 5],
    ['woord' => 'spoon', 'description' => 'An object', 'lengte' => 5],
    ['woord' => 'plate', 'description' => 'An object', 'lengte' => 5],
    ['woord' => 'brush', 'description' => 'An object', 'lengte' => 5],

    ['woord' => 'tiger', 'description' => 'An animal', 'lengte' => 5],
    ['woord' => 'zebra', 'description' => 'An animal', 'lengte' => 5],
    ['woord' => 'horse', 'description' => 'An animal', 'lengte' => 5],
    ['woord' => 'sheep', 'description' => 'An animal', 'lengte' => 5],
    ['woord' => 'mouse', 'description' => 'An animal', 'lengte' => 5],
    ['woord' => 'eagle', 'description' => 'An animal', 'lengte' => 5],
    ['woord' => 'shark', 'description' => 'An animal', 'lengte' => 5],
    ['woord' => 'whale', 'description' => 'An animal', 'lengte' => 5],
    ['woord' => 'koala', 'description' => 'An animal', 'lengte' => 5],
    ['woord' => 'panda', 'description' => 'An animal', 'lengte' => 5],

    ['woord' => 'happy', 'description' => 'An emotion', 'lengte' => 5],
    ['woord' => 'angry', 'description' => 'An emotion', 'lengte' => 5],
    ['woord' => 'proud', 'description' => 'An emotion', 'lengte' => 5],
    ['woord' => 'brave', 'description' => 'An emotion', 'lengte' => 5],
    ['woord' => 'calm',  'description' => 'An emotion', 'lengte' => 5],
    ['woord' => 'eager', 'description' => 'An emotion', 'lengte' => 5],
    ['woord' => 'tired', 'description' => 'An emotion', 'lengte' => 5],
    ['woord' => 'alert', 'description' => 'An emotion', 'lengte' => 5],
    ['woord' => 'quiet', 'description' => 'An emotion', 'lengte' => 5],
    ['woord' => 'tense', 'description' => 'An emotion', 'lengte' => 5],

    ['woord' => 'river', 'description' => 'A place', 'lengte' => 5],
    ['woord' => 'ocean', 'description' => 'A place', 'lengte' => 5],
    ['woord' => 'beach', 'description' => 'A place', 'lengte' => 5],
    ['woord' => 'field', 'description' => 'A place', 'lengte' => 5],
    ['woord' => 'forest','description' => 'A place', 'lengte' => 5],
    ['woord' => 'plain', 'description' => 'A place', 'lengte' => 5],
    ['woord' => 'vally', 'description' => 'A place', 'lengte' => 5],
    ['woord' => 'islet', 'description' => 'A place', 'lengte' => 5],
    ['woord' => 'delta', 'description' => 'A place', 'lengte' => 5],
    ['woord' => 'cliff', 'description' => 'A place', 'lengte' => 5],

    ['woord' => 'train', 'description' => 'Transport', 'lengte' => 5],
    ['woord' => 'plane', 'description' => 'Transport', 'lengte' => 5],
    ['woord' => 'truck', 'description' => 'Transport', 'lengte' => 5],
    ['woord' => 'motor', 'description' => 'Transport', 'lengte' => 5],
    ['woord' => 'scoot', 'description' => 'Transport', 'lengte' => 5],
    ['woord' => 'cable', 'description' => 'Transport', 'lengte' => 5],
    ['woord' => 'ferry', 'description' => 'Transport', 'lengte' => 5],
    ['woord' => 'canoe', 'description' => 'Transport', 'lengte' => 5],
    ['woord' => 'raft',  'description' => 'Transport', 'lengte' => 5],
    ['woord' => 'subway','description' => 'Transport', 'lengte' => 5],
]);

    db::table('muziek')->insert([
        ['titel' => 'sample', 'artiest' => 'sample', 'album' => 'sample', 'jaar' => 2026, 'genre' => 'sample', 'song_path' => 'sounds/sample.mp3']
    ]);
    }
}
