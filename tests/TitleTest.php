<?php

use App\User;

class TitleTest extends TestCase
{
    /**
     * Test if showing all titles works
     */
    public function testShowAll()
    {
        $this->get('/user/self/title', [
            'X-Auth-Token' => $this->user->token
        ]);
        $this->seeStatusCode(200)
        ->seeJsonStructure([
            'titles'
        ]);
    }

    /**
     * Test if updating all titles works
     */
    public function testUpdateAll()
    {
        // Generate 50 random titles with or without chapters
        // No Factory since these chapters come from MMD and don't follow the same names
        $titles = [];
        for ($i = 0; $i < 50; $i++) {
            // Generate chapters, or not
            $chapters = [];
            if (mt_rand(0, 100) < 50) {
                $start = mt_rand(0, 800);
                $chapters = range($start, $start+100);
            }

            $titles[$i * 100] = [
                'mal' => $i * 1000,
                'last' => mt_rand(0, 800),
                'chapters' => $chapters
            ];
        }

        $this->post('/user/self/title', [
            'titles' => $titles
        ], [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password,
            'X-Auth-Token' => $this->user->token
        ])
        ->seeStatusCode(200)
        ->seeJson([
            'status' => 'Titles list updated.',
            'inserted' => 50
        ]);
    }

    /**
     * Test if showing one title works
     */
    public function testShowSingle()
    {
        // Get a title
        factory(App\Title::class)->create([
            'user_id' => $this->user->id
        ]);
        $title = App\Title::where('user_id', $this->user->id)->firstOrFail();

        //
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

    /**
     * Test if updating one title works
     */
    public function testUpdateSingle()
    {
        // We don't care if the title doesn't exist
        $titleId = mt_rand(1, 5000);

        //
        $this->post('/user/self/title/' . $titleId, [
            'mal_id' => 1337,
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
        ]);
    }
}
