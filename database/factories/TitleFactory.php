<?php

namespace Database\Factories;

use App\Title;
use Illuminate\Database\Eloquent\Factories\Factory;

class TitleFactory extends Factory
{
	protected $model = Title::class;

	public function definition()
	{
		return [
			'mal_id' => mt_rand(1, 100000),
			'md_id' => mt_rand(1, 35000),
			'user_id' => mt_rand(1, 10),
			'last' => mt_rand(1, 100)
		];
	}
}