<?php

class HistoryTest extends TestCase
{
    public function testUpdateAll()
    {
        $history = [
            'list' => \range(112, 161),
            'titles' => []
        ];
        for ($i = 0; $i < 50; $i++) {
            $history['titles'][] = [
                'name' => bin2hex(random_bytes(20)),
                'md_id' => 112 + $i,
                'progress' => \mt_rand(0, 800),
                'chapter_id' => \mt_rand(1, 88888)
            ];
        }
        $this->post('/user/self/history', [
            'history' => $history
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'status' => 'History updated',
                'inserted' => 100
            ]);
    }

    public function testUpdateAllInvalidHistoryFieldType()
    {
        $history = [
            'list' => [ 1 ],
            'titles' => [
                'name' => 12,
                'md_id' => 1,
                'progress' => 123,
                'chapter_id' => 645
            ]
        ];
        $this->post('/user/self/history', [
            'history' => $history
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(422);
    }

    public function testUpdateAllInvalidHistoryMissingField()
    {
        $history = [
            'list' => [ 1 ],
            'titles' => [
                'name' => "12",
                'md_id' => 1,
                'progress' => 123
            ]
        ];
        $this->post('/user/self/history', [
            'history' => $history
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(422);
    }

    public function testUpdateAllNoHistory()
    {
        $this->post('/user/self/history', [
            'history' => []
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'status' => 'History updated',
                'inserted' => 0
            ]);
    }

    public function testUpdateAllEmptyRequest()
    {
        $this->post('/user/self/history', [], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'status' => 'History updated',
                'inserted' => 0
            ]);
    }

    public function testUpdateAllInvalidToken()
    {
        $this->post('/user/self/history', [], [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }

    public function testShowAll()
    {
        $this->get('/user/self/history', [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'history'
            ]);
    }

    public function testShowAllInvalidToken()
    {
        $this->get('/user/self/history', [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }

    public function testDeleteAll()
    {
        $this->delete('/user/self/history', [], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'status' => 'History deleted'
            ]);
    }

    public function testDeleteAllInvalidToken()
    {
        $this->delete('/user/self/history', [], [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }
}
