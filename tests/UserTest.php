<?php

class UserTest extends TestCase
{
    protected $newFields = [
        'password' => 'newlengthof13',
        'options' => [
            'version' => 2.4,
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
            'version' => 2.4
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
            'version' => 2.4,
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
}
