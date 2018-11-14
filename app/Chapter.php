<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'title_id'
    ];

    /**
     * Define an inverse one-to-many relationship with App\Title.
     */
    public function title()
    {
        return $this->belongsTo('App\Title');
    }
}
