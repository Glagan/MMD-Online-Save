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
    protected $username = 'user';
    protected $password = 'lengthof10';
    protected $hashedPassword;
    protected $options = [
        'version' => 2.0
    ];
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Delete users
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        DB::table('titles')->truncate();
        DB::table('chapters')->truncate();
        DB::table('history_entries')->truncate();
        DB::table('history_titles')->truncate();
        Schema::enableForeignKeyConstraints();
        // Insert default user
        $this->hashedPassword = Hash::make($this->password);
        $this->user = App\User::make([
            'username' => $this->username
        ]);
        $this->user->password = $this->hashedPassword;
        $this->user->options = \json_encode($this->options);
        $this->user->generateToken();
        $this->user->save();
    }
}
