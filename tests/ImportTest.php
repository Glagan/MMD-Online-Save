<?php

class ImportTest extends TestCase
{
	public function testImport_deprecated()
	{
		$this->post('/user/self/import', [
			'options' => [
				'saveAllOpened' => true,
				'maxChapterSaved' => 100,
				'updateHistoryPage' => true
			],
			'titles' => [
				'12' => [
					'mal' => 1245,
					'last' => 0
				],
				'1232' => [
					'mal' => 47,
					'last' => 89
				],
				'45' => [
					'last' => 999
				],
				'789' => [],
				'147' => [
					'mal' => 0,
					'last' => 12.5
				]
			],
			'history' => [
				'list' => [
					112,
					113,
					114,
					115
				],
				'titles' => [
					[
						'name' => 'La bible',
						'md_id' => 112,
						'progress' => 121,
						'chapter' => 6771
					],
					[
						'name' => 'Francois IV',
						'md_id' => 113,
						'progress' => 95,
						'chapter' => 12578
					],
					[
						'name' => 'Domo Arigator',
						'md_id' => 114,
						'progress' => 112.2,
						'chapter' => 4887
					],
					[
						'name' => 'One Piece',
						'md_id' => 115,
						'progress' => '789',
						'chapter' => 67578
					]
				]
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeJson([
				'status' => 'Data saved online',
				'options' => 'Options updated',
				'titles' => '5 title(s) imported',
				'history' => 'History updated'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 12,
				'mal_id' => 1245,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 1232,
				'mal_id' => 47,
				'last' => '89'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 45,
				'mal_id' => 0,
				'last' => '999'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 789,
				'mal_id' => 0,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 147,
				'mal_id' => 0,
				'last' => '12.5'
			])
			->seeInDatabase('history_entries', [
				'user_id' => 1,
				'md_id' => [112, 113, 114, 115]
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'name' => 'La bible',
				'md_id' => 112,
				'progress' => 121,
				'chapter' => 6771,
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'name' => 'Francois IV',
				'md_id' => 113,
				'progress' => 95,
				'chapter' => 12578,
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'name' => 'Domo Arigator',
				'md_id' => 114,
				'progress' => 112.2,
				'chapter' => 4887,
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'name' => 'One Piece',
				'md_id' => 115,
				'progress' => 789,
				'chapter' => 67578,
			]);
	}

	public function testImportOptionsOnly_deprecated()
	{
		$options = [
			'version' => 2.4,
			'saveAllOpened' => true,
			'test' => false
		];
		$this->post('/user/self/import', [
			'options' => $options
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeJson([
				'status' => 'Data saved online',
				'options' => 'Options updated',
				'titles' => '0 title(s) imported',
				'history' => 'History not updated'
			])
			->seeInDatabase('users', [
				'id' => 1,
				'options' => \json_encode($options)
			]);
	}

	public function testImportInvalidOptions_deprecated()
	{
		$this->post('/user/self/import', [
			'options' => 'string'
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(422);
	}

	public function testImportTitlesOnly_deprecated()
	{
		$this->post('/user/self/import', [
			'titles' => [
				'12' => [
					'mal' => 1245,
					'last' => 0
				],
				'1232' => [
					'mal' => 47,
					'last' => 89
				],
				'45' => [
					'last' => 999
				],
				'789' => [],
				'147' => [
					'mal' => 0,
					'last' => 12.5
				]
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeJson([
				'status' => 'Data saved online',
				'options' => 'Options not updated',
				'titles' => '5 title(s) imported',
				'history' => 'History not updated'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 12,
				'mal_id' => 1245,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 1232,
				'mal_id' => 47,
				'last' => '89'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 45,
				'mal_id' => 0,
				'last' => '999'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 789,
				'mal_id' => 0,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 147,
				'mal_id' => 0,
				'last' => '12.5'
			]);
	}

	public function testImportTitlesOnlyInvalidField_deprecated()
	{
		$this->post('/user/self/import', [
			'titles' => [
				'12' => [
					'mal' => 1245,
					'last' => 'az'
				]
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(422);
	}

	public function testImportHistoryOnly_deprecated()
	{
		$this->post('/user/self/import', [
			'history' => [
				'list' => [
					112,
					113,
					114,
					115
				],
				'titles' => [
					[
						'name' => 'La bible',
						'md_id' => 112,
						'progress' => 121,
						'chapter' => 6771
					],
					[
						'name' => 'Francois IV',
						'md_id' => 113,
						'progress' => 95,
						'chapter' => 12578
					],
					[
						'name' => 'Domo Arigator',
						'md_id' => 114,
						'progress' => 112.2,
						'chapter' => 4887
					],
					[
						'name' => 'One Piece',
						'md_id' => 115,
						'progress' => '789',
						'chapter' => 67578
					]
				]
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeJson([
				'status' => 'Data saved online',
				'options' => 'Options not updated',
				'titles' => '0 title(s) imported',
				'history' => 'History updated'
			])
			->seeInDatabase('history_entries', [
				'user_id' => 1,
				'md_id' => [112, 113, 114, 115]
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'name' => 'La bible',
				'md_id' => 112,
				'progress' => 121,
				'chapter' => 6771,
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'name' => 'Francois IV',
				'md_id' => 113,
				'progress' => 95,
				'chapter' => 12578,
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'name' => 'Domo Arigator',
				'md_id' => 114,
				'progress' => 112.2,
				'chapter' => 4887,
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'name' => 'One Piece',
				'md_id' => 115,
				'progress' => 789,
				'chapter' => 67578,
			]);
	}

	public function testImportHistoryOnlyInvalidField_deprecated()
	{
		$this->post('/user/self/import', [
			'history' => [
				'list' => [
					112
				],
				'titles' => [
					[
						'name' => 'La bible',
						'md_id' => 'string',
						'progress' => 121,
						'chapter' => 6771
					]
				]
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(422);
	}

	public function testImportInvalidAuth_deprecated()
	{
		$this->post('/user/self/import', [], [
			'X-Auth-Token' => 'invalidToken'
		])
			->seeStatusCode(401);
	}

	// v2

	public function testImport()
	{
		$this->post('/user/self/import/v2', [
			'options' => [
				'saveAllOpened' => true,
				'maxChapterSaved' => 100,
				'updateHistoryPage' => true
			],
			'titles' => [
				'112' => [
					'mal' => 1245,
					'last' => 0,
					'name' => 'La bible',
					'progress' => ['chapter' => 121],
					'chapterId' => 6771,
					'highest' => 125
				],
				'113' => [
					'mal' => 4321,
					'last' => 95,
					'name' => 'Francois IV',
					'progress' => ['chapter' => 95, 'volume' => 4],
					'chapterId' => 12578,
					'highest' => 145
				],
				'1232' => [
					'mal' => 47,
					'last' => 89
				],
				'45' => [
					'last' => 999
				],
				'789' => [],
				'147' => [
					'mal' => 0,
					'last' => 12.5
				],
				'114' => [
					'mal' => 1,
					'last' => 12.5,
					'name' => 'One Piece',
					'progress' => ['chapter' => 789],
					'chapterId' => 67578,
					'highest' => 900
				]
			],
			'history' => [112, 113, 114]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeJson([
				'status' => 'Data saved online',
				'options' => 'Options updated',
				'titles' => '7 title(s) imported',
				'history' => 'History updated'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 112,
				'mal_id' => 1245,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 113,
				'mal_id' => 4321,
				'last' => '95'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 1232,
				'mal_id' => 47,
				'last' => '89'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 45,
				'mal_id' => 0,
				'last' => '999'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 147,
				'mal_id' => 0,
				'last' => '12.5'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 114,
				'mal_id' => 1,
				'last' => '12.5'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 789,
				'mal_id' => 0,
				'last' => '0'
			])
			->seeInDatabase('history_entries', [
				'user_id' => 1,
				'md_id' => [112, 113, 114]
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'md_id' => 112,
				'name' => 'La bible',
				'progress' => '121',
				'chapter' => 6771,
				'highest' => '125'
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'md_id' => 113,
				'name' => 'Francois IV',
				'progress' => '95',
				'volume' => '4',
				'chapter' => 12578,
				'highest' => '145'
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'md_id' => 114,
				'name' => 'One Piece',
				'progress' => '789',
				'chapter' => 67578,
				'highest' => '900'
			]);
	}

	public function testImportOptionsOnly()
	{
		$options = [
			'version' => 2.4,
			'saveAllOpened' => true,
			'test' => false
		];
		$this->post('/user/self/import/v2', [
			'options' => $options
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeJson([
				'status' => 'Data saved online',
				'options' => 'Options updated',
				'titles' => '0 title(s) imported',
				'history' => 'History not updated'
			])
			->seeInDatabase('users', [
				'id' => 1,
				'options' => \json_encode($options)
			]);
	}

	public function testImportInvalidOptions()
	{
		$this->post('/user/self/import/v2', [
			'options' => 'string'
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(422);
	}

	public function testImportTitlesOnly()
	{
		$this->post('/user/self/import/v2', [
			'titles' => [
				'12' => [
					'mal' => 1245,
					'last' => 0
				],
				'1232' => [
					'mal' => 47,
					'last' => 89
				],
				'45' => [
					'last' => 999
				],
				'789' => [],
				'147' => [
					'mal' => 0,
					'last' => 12.5
				]
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeJson([
				'status' => 'Data saved online',
				'options' => 'Options not updated',
				'titles' => '5 title(s) imported',
				'history' => 'History not updated'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 12,
				'mal_id' => 1245,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 1232,
				'mal_id' => 47,
				'last' => '89'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 45,
				'mal_id' => 0,
				'last' => '999'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 789,
				'mal_id' => 0,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 147,
				'mal_id' => 0,
				'last' => '12.5'
			]);
	}

	public function testImportTitlesOnlyWithHistory()
	{
		$this->post('/user/self/import/v2', [
			'history' => [12, 1232, 45, 147],
			'titles' => [
				'12' => [
					'mal' => 1245,
					'last' => 0,
					'name' => 'One Piece',
					'progress' => [
						'chapter' => 800,
						'volume' => 90
					],
					'chapterId' => 12345,
					'lastRead' => 12345678910,
					'highest' => 944
				],
				'1232' => [
					'mal' => 47,
					'last' => 89,
					'name' => 'Vinland Saga',
					'progress' => [
						'chapter' => 45,
						'volume' => 5
					],
					'chapterId' => 54321,
					'highest' => 84
				],
				'45' => [
					'last' => 999,
					'name' => 'Pokémon',
					'progress' => [
						'chapter' => 4
					],
					'chapterId' => 33445
				],
				'789' => [],
				'888' => [
					'mal' => 0,
					'last' => 12,
					'name' => 'La Bible',
					'progress' => [
						'chapter' => 8
					],
					'chapterId' => 7545,
					'lastRead' => 12345678910,
				],
				'147' => [
					'mal' => 0,
					'last' => 12.5,
					'name' => 'FMA',
					'progress' => [
						'chapter' => 80
					],
					'chapterId' => 78545,
					'lastRead' => 12345678910,
				]
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeJson([
				'status' => 'Data saved online',
				'options' => 'Options not updated',
				'titles' => '6 title(s) imported',
				'history' => 'History updated'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 12,
				'mal_id' => 1245,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 1232,
				'mal_id' => 47,
				'last' => '89'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 45,
				'mal_id' => 0,
				'last' => '999'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 789,
				'mal_id' => 0,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 147,
				'mal_id' => 0,
				'last' => '12.5'
			])
			->seeInDatabase('history_entries', [
				'user_id' => 1,
				'md_id' => [12, 1232, 45, 147]
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'md_id' => 12,
				'name' => 'One Piece',
				'progress' => '800',
				'volume' => '90',
				'chapter' => 12345,
				'lastRead' => 12345678910,
				'highest' => '944'
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'name' => 'Vinland Saga',
				'progress' => '45',
				'volume' => '5',
				'chapter' => 54321,
				'highest' => '84'
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'md_id' => 45,
				'name' => 'Pokémon',
				'progress' => '4',
				'chapter' => 33445
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'md_id' => 888,
				'name' => 'La Bible',
				'progress' => '8',
				'chapter' => 7545,
				'lastRead' => 12345678910,
			])
			->seeInDatabase('history_titles', [
				'user_id' => 1,
				'md_id' => 147,
				'name' => 'FMA',
				'progress' => '80',
				'chapter' => 78545,
				'lastRead' => 12345678910,
			]);
	}

	public function testImportTitlesOnlyInvalidField()
	{
		$this->post('/user/self/import/v2', [
			'titles' => [
				'12' => [
					'mal' => 1245,
					'last' => 'az'
				]
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(422);
	}

	public function testImportTitlesInvalidKeyIgnored()
	{
		$this->post('/user/self/import/v2', [
			'titles' => [
				'12' => [
					'mal' => 1245,
					'last' => 0
				],
				'undefined' => [
					'mal' => 47,
					'last' => 89
				],
				'789' => [],
				'147' => [
					'mal' => 0,
					'last' => 12.5
				],
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeJson([
				'status' => 'Data saved online',
				'options' => 'Options not updated',
				'titles' => '3 title(s) imported',
				'history' => 'History not updated'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 12,
				'mal_id' => 1245,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 789,
				'mal_id' => 0,
				'last' => '0'
			])
			->seeInDatabase('titles', [
				'user_id' => 1,
				'md_id' => 147,
				'mal_id' => 0,
				'last' => '12.5'
			]);
	}
	public function testImportTitlesInvalidChapters()
	{
		$this->post('/user/self/import/v2', [
			'titles' => [
				'1354' => [
					'mal' => 0,
					'last' => 12.5,
					'chapters' => 12
				]
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(422);
	}

	public function testImportMissingHistoryChapter()
	{
		$this->post('/user/self/import/v2', [
			'options' => [
				'saveAllOpened' => true,
				'maxChapterSaved' => 100,
				'updateHistoryPage' => true
			],
			'titles' => [
				'114' => [
					'mal' => 1,
					'last' => 12.5,
					'name' => 'One Piece',
					'md_id' => 115,
					'progress' => ['volume' => 12],
					'chapterId' => 67578
				]
			],
			'history' => [114]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeInDatabase('history_entries', [
				'user_id' => 1,
				'md_id' => 114,
			])
			->notSeeInDatabase('history_titles', [
				'user_id' => 1,
				'md_id' => 114,
			]);
	}

	public function testImportHistoryOnly()
	{
		$this->post('/user/self/import/v2', [
			'history' => [112, 113, 114, 115],
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(200)
			->seeJson([
				'status' => 'Data saved online',
				'options' => 'Options not updated',
				'titles' => '0 title(s) imported',
				'history' => 'History updated'
			])
			->seeInDatabase('history_entries', [
				'user_id' => 1,
				'md_id' => [112, 113, 114, 115]
			]);
	}

	public function testImportHistoryOnlyInvalidField()
	{
		$this->post('/user/self/import/v2', [
			'history' => [
				112,
				'string'
			]
		], [
			'X-Auth-Token' => $this->user->token
		])
			->seeStatusCode(422);
	}

	public function testImportInvalidAuth()
	{
		$this->post('/user/self/import/v2', [], [
			'X-Auth-Token' => 'invalidToken'
		])
			->seeStatusCode(401);
	}
}
