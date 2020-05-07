<?php

class RegisterTest extends TestCase
{
    public function testWorkingRegisterWithOptions()
    {
        $options = [
            'version' => 2.4,
            'key' => 'value'
        ];
        $this->post('/user', [
            'username' => 'user2',
            'password' => $this->password,
            'options' => $options
        ])
            ->seeStatusCode(201)
            ->seeJsonStructure([
                'token'
            ])
            ->seeJson([
                'status' => 'Account created'
            ])
            ->seeInDatabase('users', [
                'id' => 2,
                'username' => 'user2',
                'options' => \json_encode($options)
            ]);
    }

    public function testWorkingRegisterWithoutOptions()
    {
        $this->post('/user', [
            'username' => 'user2',
            'password' => $this->password
        ])
            ->seeStatusCode(201)
            ->seeJsonStructure([
                'token'
            ])
            ->seeJson([
                'status' => 'Account created'
            ])
            ->seeInDatabase('users', [
                'id' => 2,
                'username' => 'user2'
            ]);
        }

    public function testRegisterUserAlreadyExist()
    {
        $this->post('/user', [
            'username' => $this->username,
            'password' => $this->password
        ])
            ->seeStatusCode(422);
    }

    public function testRegisterMissingFieldUsername()
    {
        $this->post('/user', [
            'password' => 'secretsecret'
        ])
            ->seeStatusCode(422);
    }

    public function testRegisterMissingFieldPassword()
    {
        $this->post('/user', [
            'username' => 'invalidUser',
        ])
            ->seeStatusCode(422);
    }

    public function testRegisteInvalidPassword()
    {
        $this->post('/user', [
            'username' => 'invalidUser',
            'password' => 'small'
        ])
            ->seeStatusCode(422);
    }
}