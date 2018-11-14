<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Hash;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'token', 'options', 'titles', 'last_sync', 'last_update', 'creation_date'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_sync' => 'datetime',
        'last_update' => 'datetime',
        'creation_date' => 'datetime',
    ];

    /**
     * Define a one-to-many relationship with App\Title
     */
    public function titles()
    {
        return $this->hasMany('App\Title');
    }

    public function generateToken()
    {
        $this->token = bin2hex(random_bytes(25));
        return $this;
    }
}
