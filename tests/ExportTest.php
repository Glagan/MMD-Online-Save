<?php

use App\HistoryEntry;
use App\HistoryTitle;
use App\Title;

class ExportTest extends TestCase
{
	public function testExport_deprecated()
	{
		HistoryEntry::insert([
			['user_id' => 1, 'md_id' => 112],
			['user_id' => 1, 'md_id' => 113],
			['user_id' => 1, 'md_id' => 114],
		]);
		Title::insert([
			[
				'user_id' => 1,
				'mal_id' => 1000,
				'md_id' => 112,
				'last' => 100,
			],
			[
				'user_id' => 1,
				'mal_id' => 1001,
				'md_id' => 113,
				'last' => 101,
			],
			[
				'user_id' => 1,
				'mal_id' => 1002,
				'md_id' => 114,
				'last' => 102,
			],
		]);
		HistoryTitle::insert([
			[
				'user_id' => 1,
				'name' => 'One Piece',
				'md_id' => 112,
				'progress' => 12,
				'volume' => 12,
				'chapter' => 67578,
				'lastRead' => 12345678910,
				'highest' => 900
			],
			[
				'user_id' => 1,
				'name' => 'Naruto',
				'md_id' => 113,
				'progress' => 13,
				'volume' => 14,
				'chapter' => 67574,
				'lastRead' => 12345678911,
				'highest' => 800
			],
			[
				'user_id' => 1,
				'name' => 'Yu-Gi-Oh',
				'md_id' => 114,
				'progress' => 18,
				'volume' => 15,
				'chapter' => 4545,
				'lastRead' => 13345678910,
				'highest' => 500
			],
		]);

		$this->get('/user/self/export', [
			'X-Auth-Token' => $this->user->token,
		])
			->seeStatusCode(200)
			->seeJsonStructure([
				'options',
				'titles',
				'history'
			])

			->seeJson([
				'history' => [
					'list' => [112, 113, 114],
					'titles' => [
						[
							'chapter' => 4545,
							'md_id' => 114,
							'name' => 'Yu-Gi-Oh',
							'progress' => ['chapter' => '18', 'volume' => '15']
						],
						[
							'chapter' => 67574,
							'md_id' => 113,
							'name' => 'Naruto',
							'progress' => ['chapter' => '13', 'volume' => '14']
						],
						[
							'chapter' => 67578,
							'md_id' => 112,
							'name' => 'One Piece',
							'progress' => ['chapter' => '12', 'volume' => '12']
						]
					]
				],
				'titles' => [
					[
						'chapters' => [],
						'last' => 102,
						'mal_id' => 1002,
						'md_id' => 114,
					], [
						'chapters' => [],
						'last' => 101,
						'mal_id' => 1001,
						'md_id' => 113,
					], [
						'chapters' => [],
						'last' => 100,
						'mal_id' => 1000,
						'md_id' => 112,
					]
				]
			]);
	}

	public function testExportInvalidAuth_deprecated()
	{
		$this->get('/user/self/export', [
			'X-Auth-Token' => 'invalidToken'
		])
			->seeStatusCode(401);
	}

	// v2

	public function testExport()
	{
		HistoryEntry::insert([
			['user_id' => 1, 'md_id' => 112],
			['user_id' => 1, 'md_id' => 113],
			['user_id' => 1, 'md_id' => 114],
		]);
		Title::insert([
			[
				'user_id' => 1,
				'mal_id' => 1000,
				'md_id' => 112,
				'last' => 100,
			],
			[
				'user_id' => 1,
				'mal_id' => 1001,
				'md_id' => 113,
				'last' => 101,
			],
			[
				'user_id' => 1,
				'mal_id' => 1002,
				'md_id' => 114,
				'last' => 102,
			],
		]);
		HistoryTitle::insert([
			[
				'user_id' => 1,
				'name' => 'One Piece',
				'md_id' => 112,
				'progress' => 12,
				'volume' => 12,
				'chapter' => 67578,
				'lastRead' => 12345678910,
				'highest' => 900
			],
			[
				'user_id' => 1,
				'name' => 'Naruto',
				'md_id' => 113,
				'progress' => 13,
				'volume' => 14,
				'chapter' => 67574,
				'lastRead' => 12345678911,
				'highest' => 800
			],
			[
				'user_id' => 1,
				'name' => 'Yu-Gi-Oh',
				'md_id' => 114,
				'progress' => 18,
				'volume' => 15,
				'chapter' => 4545,
				'lastRead' => 13345678910,
				'highest' => 500
			],
		]);

		$this->get('/user/self/export/v2', [
			'X-Auth-Token' => $this->user->token,
		])
			->seeStatusCode(200)
			->seeJsonStructure([
				'options',
				'titles',
				'history'
			])
			->seeJson([
				'history' => [112, 113, 114],
				'titles' => [
					[
						'chapterId' => 4545,
						'chapters' => [],
						'highest' => '500',
						'last' => 102,
						'lastRead' => 13345678910,
						'mal_id' => 1002,
						'md_id' => 114,
						'name' => 'Yu-Gi-Oh',
						'progress' => ['chapter' => '18', 'volume' => '15']
					], [
						'chapterId' => 67574,
						'chapters' => [],
						'highest' => '800',
						'last' => 101,
						'lastRead' => 12345678911,
						'mal_id' => 1001,
						'md_id' => 113,
						'name' => 'Naruto',
						'progress' => ['chapter' => '13', 'volume' => '14']
					], [
						'chapterId' => 67578,
						'chapters' => [],
						'highest' => '900',
						'last' => 100,
						'lastRead' => 12345678910,
						'mal_id' => 1000,
						'md_id' => 112,
						'name' => 'One Piece',
						'progress' => ['chapter' => '12', 'volume' => '12']
					]
				]
			]);
	}

	public function testExportInvalidAuth()
	{
		$this->get('/user/self/export', [
			'X-Auth-Token' => 'invalidToken'
		])
			->seeStatusCode(401);
	}
}
