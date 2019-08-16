<?php

class UserTest extends TestCase
{
    protected $newFields = [
        'password' => 'newlengthof13',
        'options' => [
            'version' => 2.0,
            'updated' => true
        ]
    ];

    protected $newOptions = [
        'options' => '{"version":2.1,"status":false}'
    ];

    public function testGetUser()
    {
        $this->get('/user/self', [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'username',
                'token',
                'options',
                'last_sync',
                'creation_date',
                'last_update',
            ])
            ->seeJson([
                'username' => $this->username,
                'token' => $this->user->token,
                'options' => $this->options
            ]);
    }

    public function testGetUserInvalidToken()
    {
        $this->get('/user/self', [
            'X-Auth-Token' => 'notatoken'
        ])
            ->seeStatusCode(401);
    }

    public function testGetUserEmptyToken()
    {
        $this->get('/user/self')
            ->seeStatusCode(401);
    }

    public function testUpdateUser()
    {
        $newOptions = [
            'version' => 2.0
        ];
        $this->post('/user/self', [
            'password' => 'newlengthof13',
            'options' => $newOptions
        ], [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status',
                'token',
                'options'
            ])
            ->seeJson([
                'status' => 'User updated',
                'options' => $newOptions
            ]);
    }

    public function testUpdateUserNoOptions()
    {
        $this->post('/user/self', [
            'password' => 'newlengthof13'
        ], [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status',
                'token',
                'options'
            ])
            ->seeJson([
                'status' => 'User updated',
                'options' => $this->options
            ]);
    }

    public function testUpdateUserInvalidFields()
    {
        $this->post('/user/self', [
            'password' => 'toosmall',
        ], [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(422);
    }

    public function testUpdateUserInvalidAuthField()
    {
        $this->post('/user/self', $this->newFields, [
            'X-Auth-Name' => $this->username
        ])
            ->seeStatusCode(400);
    }

    public function testUpdateUserInvalidAuthUsername()
    {
        $this->post('/user/self', $this->newFields, [
            'X-Auth-Name' => 'invalidUser',
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(400);
    }

    public function testUpdateUserInvalidAuthPassword()
    {
        $this->post('/user/self', $this->newFields, [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => 'invalidpassword'
        ])
            ->seeStatusCode(400);
    }

    public function testUpdateUserNoAuth()
    {
        $this->post('/user/self', $this->newFields)
            ->seeStatusCode(400);
    }

    public function testDeleteUser()
    {
        $this->delete('/user/self', [], [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'status' => 'User deleted'
            ]);
    }

    public function testDeleteUserInvalidAuthField()
    {
        $this->delete('/user/self', [], [
            'X-Auth-Name' => $this->username
        ])
            ->seeStatusCode(400);
    }

    public function testDeleteUserInvalidAuthUsername()
    {
        $this->delete('/user/self', [], [
            'X-Auth-Name' => 'invalidUser',
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(400);
    }

    public function testDeleteUserInvalidAuthPassword()
    {
        $this->delete('/user/self', [], [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => 'invalidpassword'
        ])
            ->seeStatusCode(400);
    }

    public function testDeleteUserNoAuth()
    {
        $this->delete('/user/self', [])
            ->seeStatusCode(400);
    }

    public function testGetUserOptions()
    {
        $this->get('/user/self/options', [
            'X-Auth-Token' => $this->user->token,
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'options' => $this->options
            ]);
    }

    public function testGetUserOptionsInvalidAuthField()
    {
        $this->get('/user/self/options', [
            'X-Auth-Name' => $this->username
        ])
            ->seeStatusCode(401);
    }

    public function testGetUserOptionsInvalidAuthUsername()
    {
        $this->get('/user/self/options', [
            'X-Auth-Name' => 'invalidUser',
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(401);
    }

    public function testGetUserOptionsInvalidAuthPassword()
    {
        $this->get('/user/self/options', [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => 'invalidpassword'
        ])
            ->seeStatusCode(401);
    }

    public function testGetUserOptionsNoAuth()
    {
        $this->get('/user/self/options')
            ->seeStatusCode(401);
    }

    public function testPostUserOptions()
    {
        $newOptions = [
            'version' => 2.0,
            'color' => 'red',
        ];
        $this->post('/user/self/options', [
            'options' => $newOptions
        ], [
            'X-Auth-Token' => $this->user->token,
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'status' => 'Options saved',
                'options' => $newOptions
            ]);
    }

    public function testPostUserOptionsInvalidAuthField()
    {
        $this->post('/user/self/options', $this->newOptions, [
            'X-Auth-Name' => $this->username
        ])
            ->seeStatusCode(401);
    }

    public function testPostUserOptionsInvalidAuthUsername()
    {
        $this->post('/user/self/options', $this->newOptions, [
            'X-Auth-Name' => 'invalidUser',
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(401);
    }

    public function testPostUserOptionsInvalidAuthPassword()
    {
        $this->post('/user/self/options', $this->newOptions, [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => 'invalidpassword'
        ])
            ->seeStatusCode(401);
    }

    public function testPostUserOptionsNoAuth()
    {
        $this->post('/user/self/options', $this->newOptions)
            ->seeStatusCode(401);
    }

    public function testExport()
    {
        $this->get('/user/self/export', [
            'X-Auth-Token' => $this->user->token,
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'options',
                'titles',
                'history'
            ]);
    }

    public function testExportInvalidAuth()
    {
        $this->get('/user/self/export', [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }

    public function testImport()
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
                'status' => 'Data imported',
                'options' => 'Options updated',
                'titles' => '5 title(s) imported',
                'history' => 'History updated'
            ]);
    }

    public function testImportOptionsOnly()
    {
        $options = [
            'version' => '2.0',
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
                'status' => 'Data imported',
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
        $this->post('/user/self/import', [
            'options' => 'string'
        ], [
            'X-Auth-Token' => $this->user->token
        ])
            ->seeStatusCode(422);
    }

    public function testImportTitlesOnly()
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
                'status' => 'Data imported',
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

    public function testImportTitlesOnlyInvalidField()
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

    public function testImportHistoryOnly()
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
                'status' => 'Data imported',
                'options' => 'Options not updated',
                'titles' => '0 title(s) imported',
                'history' => 'History updated'
            ])
            ->seeInDatabase('history_entries', [
                'user_id' => 1,
                'md_id' => [ 112, 113, 114, 115 ]
            ])
            ->seeInDatabase('history_titles', [
                'user_id' => 1,
                'name' => 'La bible',
                'md_id' => 112,
                'progress' => '121',
                'chapter' => 6771,
            ])
            ->seeInDatabase('history_titles', [
                'user_id' => 1,
                'name' => 'Francois IV',
                'md_id' => 113,
                'progress' => '95',
                'chapter' => 12578,
            ])
            ->seeInDatabase('history_titles', [
                'user_id' => 1,
                'name' => 'Domo Arigator',
                'md_id' => 114,
                'progress' => '112.2',
                'chapter' => 4887,
            ])
            ->seeInDatabase('history_titles', [
                'user_id' => 1,
                'name' => 'One Piece',
                'md_id' => 115,
                'progress' => '789',
                'chapter' => 67578,
            ]);
    }

    public function testImportHistoryOnlyInvalidField()
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

    public function testImportInvalidAuth()
    {
        $this->post('/user/self/import', [], [
            'X-Auth-Token' => 'invalidToken'
        ])
            ->seeStatusCode(401);
    }
}
