<?php

use App\User;

class RegisterTest extends TestCase
{
    /**
     * Test if user registration works
     */
    public function testRegister()
    {
        $this->post('/user', [
            'username' => 'user2',
            'email' => 'unique.special.email@example.org',
            'password' => 'secretsecret'
        ])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'token'
        ])
        ->seeJson([
            'status' => 'Account created.'
        ]);
    }

    /**
     * Test if token refresh works
     */
    public function testGetTokenRefresh()
    {
        $this->get('/user/self/token/refresh', [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password,
        ])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'token'
        ])
        ->seeJson([
            'status' => 'Token updated.'
        ]);
    }

    /**
     * Test if getting an user informations works
     */
    public function testGetUser()
    {
        $this->get('/user/self', [
            'X-Auth-Token' => $this->user->token,
        ])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'username',
            'email',
            'token',
            'options',
            'last_sync',
            'creation_date',
            'last_update',
        ])
        ->seeJson([
            'username' => $this->username,
            'email' => $this->email,
            'token' => $this->user->token,
            'options' => $this->user->options
        ]);
    }

    /**
     * Test if updating an user works
     */
    public function testPostUser()
    {
        $this->post('/user/self', [
            'password' => 'newlengthof13',
            'email' => 'new.email@provider.com',
            'options' => '{version:2.0}'
        ], [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password,
            'X-Auth-Token' => $this->user->token,
        ])
        ->seeStatusCode(200)
        ->seeJson([
            'status' => 'User updated.'
        ]);

        // Restore back the test user
        $this->restoreTestUser();
    }

    /**
     * Test if deleting an user works
     */
    public function testDeleteUser()
    {
        $this->delete('/user/self', [], [
            'X-Auth-Name' => $this->username,
            'X-Auth-Pass' => $this->password,
            'X-Auth-Token' => $this->user->token,
        ])
        ->seeStatusCode(200)
        ->seeJson([
            'status' => 'User deleted.'
        ]);

        // Restore back the test user
        $this->restoreTestUser();
    }

    /**
     * Test if getting an user options works
     */
    public function testGetUserOptions()
    {
        $this->get('/user/self/options', [
            'X-Auth-Token' => $this->user->token,
        ])
        ->seeStatusCode(200)
        ->seeJson([
            'options' => $this->user->options
        ]);
    }

    /**
     * Test if updating an user options works
     */
    public function testPostUserOptions()
    {
        $this->post('/user/self/options', [
            'options' => '{version:2.0}'
        ], [
            'X-Auth-Token' => $this->user->token,
        ])
        ->seeStatusCode(200)
        ->seeJson([
            'status' => 'Options saved.'
        ]);
    }
}
