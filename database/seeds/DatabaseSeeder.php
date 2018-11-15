<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Title;
use App\Chapter;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checking because truncate() will fail
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();
        Title::truncate();
        Chapter::truncate();

        factory(User::class, 10)->create()->each(function ($user) {
            factory(Title::class, 10)->create([
                'user_id' => $user->id,
            ])->each(function ($title) {
                $chapters = [];

                // Create all chapters
                $base = $title->last;
                $max = $base + 100;
                for (; $base < $max; $base++) {
                    $chapters[] = [
                        'title_id' => $title->id,
                        'value' => $base
                    ];
                }

                // save all at once
                Chapter::insert($chapters);
            });
        });

        // Enable it back
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
