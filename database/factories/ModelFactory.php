<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Faker\Generator;

$factory->define(App\User::class, function (Generator $faker) {
    $hasher = app()->make('hash');

    return [
        'username' => $faker->unique()->word,
        'email' => $faker->unique()->safeEmail,
        'password' => $hasher->make('secretsecret'),
        //'options' => '{}',
        'token' => bin2hex(random_bytes(25)),
    ];
});

$factory->define(App\Title::class, function (Generator $faker) {
    return [
        'mal_id' => mt_rand(1, 100000),
        'md_id' => mt_rand(1, 35000),
        'user_id' => mt_rand(1, 10),
        'last' => mt_rand(1, 100)
    ];
});

$factory->define(App\Chapter::class, function (Generator $faker) {
    return [
        'title_id' => mt_rand(1, 100),
        'value' => mt_rand(1, 850)
    ];
});