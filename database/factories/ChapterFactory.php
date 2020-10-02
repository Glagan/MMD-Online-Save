<?php

namespace Database\Factories;

use App\Chapter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChapterFactory extends Factory
{
	protected $model = Chapter::class;

	public function definition()
	{
		return [
			'title_id' => mt_rand(1, 100),
			'value' => mt_rand(1, 850)
		];
	}
}