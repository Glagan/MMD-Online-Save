<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryEntry extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'md_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'user_id'
    ];

    /**
     * Define an inverse one-to-many relationship with App\User.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
