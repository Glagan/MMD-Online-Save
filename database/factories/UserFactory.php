<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
	protected $model = User::class;

	public function definition()
	{
		$hasher = app()->make('hash');

		return [
			'username' => $this->faker->unique()->word,
			'password' => $hasher->make('secretsecret'),
			//'options' => '{}',
			'token' => bin2hex(random_bytes(25)),
		];
	}
}