<?php

class TokenTest extends TestCase
{
    /**
     * SHOW TOKEN
     */

    public function testShowToken()
    {
        $this->get('/user/self/token', [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password,
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'token'
            ])
            ->seeJson([
                'token' => $this->user->token
            ]);
    }

    public function testShowTokenAuthMissingField()
    {
        $this->get('/user/self/token', [
            'X-Auth-Name' => $this->username
        ])
            ->seeStatusCode(400);
    }

    public function testShowTokenAuthInvalidUser()
    {
        $this->get('/user/self/token', [
            'X-Auth-Name' => 'invalidUser',
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(400);
    }

    public function testShowTokenAuthInvalidPassword()
    {
        $this->get('/user/self/token', [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => 'invalid'
        ])
            ->seeStatusCode(400);
    }

    public function testShowTokenNoAuth()
    {
        $this->get('/user/self/token')
            ->seeStatusCode(400);
    }

    /**
     * TOKEN REFRESH
     */

    public function testTokenRefresh()
    {
        $this->get('/user/self/token/refresh', [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'token'
            ])
            ->seeJson([
                'status' => 'Token updated.'
            ])
            ->seeInDatabase('users', [
                'token' => Auth::user()->token
            ]);
    }

    public function testTokenRefreshAuthMissingField()
    {
        $this->get('/user/self/token/refresh', [
            'X-Auth-Name' => $this->username
        ])
            ->seeStatusCode(400);
    }

    public function testTokenRefreshAuthInvalidUser()
    {
        $this->get('/user/self/token/refresh', [
            'X-Auth-Name' => 'invalidUser',
            'X-Auth-Pass' => $this->password
        ])
            ->seeStatusCode(400);
    }

    public function testTokenRefreshAuthInvalidPassword()
    {
        $this->get('/user/self/token/refresh', [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => 'invalid'
        ])
            ->seeStatusCode(400);
    }

    public function testTokenRefreshNoAuth()
    {
        $this->get('/user/self/token/refresh')
            ->seeStatusCode(400);
    }
}