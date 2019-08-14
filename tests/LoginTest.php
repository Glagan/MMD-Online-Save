<?php

class LoginTest extends TestCase
{
    public function testWorkingLogin()
    {
        $this->get('/user', [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password,
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'status' => 'Correct credentials',
                'token' => $this->user->token
            ]);
    }

    public function testLoginAuthMissingField()
    {
        $this->get('/user', [
            'X-Auth-Name' => $this->username,
        ])
            ->seeStatusCode(400);
    }

    public function testLoginInvalidAuthUsername()
    {
        $this->get('/user', [
            'X-Auth-Name' => 'invalidUser',
            'X-Auth-Pass' => $this->password,
        ])
            ->seeStatusCode(400);
    }

    public function testLoginInvalidAuthPassword()
    {
        $this->get('/user', [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => 'oofoofoofoof',
        ])
            ->seeStatusCode(400);
    }

    public function testLoginNoAuth()
    {
        $this->get('/user')
            ->seeStatusCode(400);
    }
}