<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryTitle extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'md_id', 'progress', 'chapter'
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
