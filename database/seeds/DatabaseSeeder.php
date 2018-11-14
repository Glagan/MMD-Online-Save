<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Title;
use App\Chapter;

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

        factory(User::class, 50)->create();
        factory(Title::class, 250)->create();
        factory(Chapter::class, 2000)->create();

        // Enable it back
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
