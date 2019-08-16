<?php

use App\Title;

class TitleTest extends TestCase
{
    protected function getTitle()
    {
        factory(App\Title::class)->create([
            'user_id' => $this->user->id
        ]);
        return App\Title::where('user_id', $this->user->id)->firstOrFail();
    }

    protected function getTitles($amount)
    {
        // Generate x random titles with or without chapters
        // No Factory since these chapters come from MMD and don't follow the same names
        $titles = [];
        for ($i = 0; $i < $amount; $i++) {
            // Generate chapters, or not
            $chapters = [];
            if (\mt_rand(0, 100) < $amount) {
                $start = \mt_rand(0, 800);
                $chapters = \range($start, $start + 100);
            }

            $titles[$i * 100] = [
                'mal' => $i * 1000,
                'last' => \mt_rand(0, 800),
                'chapters' => $chapters
            ];
        }
        return $titles;
    }

    public function testShowAll()
    {
        $this->get('/user/self/title', [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'titles'
            ]);
    }

    public function testShowAllInvalidToken()
    {
        $this->get('/user/self/title', [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }

    public function testUpdateAll()
    {
        $titles = $this->getTitles(50);
        $this->post('/user/self/title', [
            'titles' => $titles
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'status' => '50 title(s) inserted',
                'inserted' => 50
            ])
            ->seeInDatabase('titles', [
                'user_id' => Auth::user()->id,
                'mal_id' => \array_column($titles, 'mal')
            ]);
    }

    public function testUpdateAllInvalidTitles()
    {
        $this->post('/user/self/title', [
            'titles' => [
                'mal' => 1,
                'last' => 'az',
                'chapters' => [1, 2, 3]
            ]
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(422);
    }

    public function testUpdateAllInvalidToken()
    {
        $this->post('/user/self/title', [
            'titles' => $this->getTitles(50)
        ], [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }

    public function testShowSingle()
    {
        $title = $this->getTitle();
        $this->get('/user/self/title/' . $title->md_id, [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'mal_id',
                'md_id',
                'last',
                'chapters'
            ]);
    }

    public function testShowSingleEmptyTitle()
    {
        $this->get('/user/self/title/999999', [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeJsonStructure([
                'status'
            ])
            ->seeStatusCode(404);
    }

    public function testShowSingleInvalidToken()
    {
        $title = $this->getTitle();
        $this->get('/user/self/title/' . $title->md_id, [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }

    public function testUpdateSingle()
    {
        $this->post('/user/self/title/12', [
            'mal' => 1337,
            'last' => 999,
            'options' => [
                'saveAllOpened' => true,
                'maxChapterSaved' => 100
            ]
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status'
            ])
            ->seeJson([
                'last' => 999
            ])
            ->seeInDatabase('titles', [
                'user_id' => Auth::user()->id,
                'mal_id' => 1337,
                'md_id' => 12,
                'last' => '999'
            ]);
    }

    public function testUpdateSingleWithChapters()
    {
        $this->post('/user/self/title/12', [
            'mal' => 1337,
            'last' => 999,
            'options' => [
                'saveAllOpened' => true,
                'maxChapterSaved' => 100
            ],
            'chapters' => [ 1, 2, 3 ]
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status'
            ])
            ->seeJson([
                'last' => 999
            ])
            ->seeInDatabase('titles', [
                'user_id' => Auth::user()->id,
                'mal_id' => 1337,
                'md_id' => 12,
                'last' => '999'
            ])
            ->seeInDatabase('chapters', [
                'title_id' => 1,
                'value' => ['1', '2', '3']
            ]);
    }

    public function testUpdateSingleInvalidField()
    {
        $this->post('/user/self/title/12', [
            'mal' => 1337,
            'last' => 'az',
            'options' => [
                'saveAllOpened' => true,
                'maxChapterSaved' => 100
            ]
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(422);
    }

    public function testUpdateSingleInvalidOption()
    {
        $this->post('/user/self/title/12', [
            'mal' => 1337,
            'last' => 123,
            'options' => [
                'saveAllOpened' => 'not_false',
                'maxChapterSaved' => 100
            ]
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(422);
    }

    public function testUpdateSingleInvalidAuth()
    {
        $this->post('/user/self/title/1245', [
            'mal' => 1337,
            'last' => 999,
            'options' => [
                'saveAllOpened' => true,
                'maxChapterSaved' => 100
            ]
        ], [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }

    public function testDeleteSingle()
    {
        // Create en save a title
        $title = Title::make([
            'md_id' => 12,
            'mal_id' => 1,
            'last' => 123
        ]);
        $title->user_id = 1;
        $title->save();
        // Delete it
        $this->delete('/user/self/title/12', [], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'status' => 'Title #12 deleted'
            ]);
    }

    public function testDeleteSingleEmptyTitle()
    {
        $this->delete('/user/self/title/9999999', [], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(404);
    }

    public function testDeleteSingleInvalidAuth()
    {
        $this->delete('/user/self/title/12', [], [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }

    public function testDeleteAll()
    {
        // Create en save a title
        $title = Title::make([
            'md_id' => 12,
            'mal_id' => 1,
            'last' => 123
        ]);
        $title->user_id = 1;
        $title->save();
        // Delete all of them
        $this->delete('/user/self/title', [], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'status' => 'Deleted 1 title(s)'
            ]);
    }

    public function testDeleteAllInvalidAuth()
    {
        $this->delete('/user/self/title', [], [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }

    public function testUpdateSingleHistory()
    {
        $this->post('/user/self/title/12', [
            'mal' => 1337,
            'last' => 999,
            'options' => [
                'saveAllOpened' => true,
                'maxChapterSaved' => 100,
                'updateHistoryPage' => true
            ],
            'title_name' => 'One Piece',
            'chapter' => 16454
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status'
            ])
            ->seeJson([
                'last' => 999
            ])
            ->seeInDatabase('titles', [
                'user_id' => Auth::user()->id,
                'mal_id' => 1337,
                'md_id' => 12,
                'last' => '999'
            ])
            ->seeInDatabase('history_titles', [
                'user_id' => Auth::user()->id,
                'name' => 'One Piece',
                'md_id' => 12,
                'progress' => '999',
                'chapter' => 16454
            ]);
    }

    public function testUpdateSingleHistoryInvalidField()
    {
        $this->post('/user/self/title/12', [
            'mal' => 1337,
            'last' => 999,
            'options' => [
                'saveAllOpened' => true,
                'maxChapterSaved' => 100,
                'updateHistoryPage' => true
            ],
            'title_name' => 'One Piece'
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(422);
    }
}
