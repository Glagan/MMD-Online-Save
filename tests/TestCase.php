<?php

use Illuminate\Support\Facades\Hash;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Add a user property "user" with a test App\User inside
     */
    protected $username = 'user1';
    protected $password = 'lengthof10';
    protected $email = 'unique@provider.com';
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->restoreTestUser();
    }

    public function restoreTestUser()
    {
        // Delete if already exist
        $this->user = App\User::where('username', $this->username)->first();
        if ($this->user) {
            $this->user->delete();
        }

        // Save a new one
        $this->user = App\User::make([
            'username' => $this->username,
            'email' => $this->email
        ]);
        $this->user->generateToken();
        $this->user->password = Hash::make($this->password);
        $this->user->save();
    }
}
