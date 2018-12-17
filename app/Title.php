<?php namespace App;

use App\Chapter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Title extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mal_id', 'md_id', 'last', 'chapters'
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last' => 'float'
    ];

    /**
     * Define an inverse one-to-many relationship with App\User.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Define a one-to-many relationship with App\Chapter
     */
    public function chapters()
    {
        return $this->hasMany('App\Chapter');
    }

    /**
     * Check if a title already has a chapter
     */
    public function hasChapter($chapter)
    {
        return (Chapter::where('title_id', '=', $this->id)->where('value', '=', $chapter)->first() != null);
    }

    public function sortedChapters($order = 'ASC')
    {
        return $this->chapters()->orderBy(DB::raw('value*1'), $order);
    }

    public function insertChapter($chapterValue)
    {
        $chapter = new Chapter;
        $chapter->title_id = $this->id;
        $chapter->value = $chapterValue;
        $chapter->save();

        return $this;
    }

    public function addChapterRange($from, $to)
    {
        $chapters = [];
        for ($i = $from; $i <= $to; $i++) {
            $chapters[] = [
                'title_id' => $this->id,
                'value' => $i,
            ];
        }
        Chapter::insert($chapters);

        return $this;
    }

    /**
     * Insert a chapter at the correct place in the sorted array.
     * @source https://stackoverflow.com/questions/9524501/what-are-better-ways-to-insert-element-in-sorted-array-in-php
     */
    /*public function insertChapter($chapter) {
        // Unique
        if (in_array($chapter, $this->chapters))
            return;

        // If empty just push and return
        $stopIndex = count($this->chapters) - 1;
        if ($stopIndex < 0) {
            $this->chapters[] = $chapter;
            return $this;
        }

        // Sorted insert
        $startIndex = 0;
        $middle = 0;
        while ($startIndex < $stopIndex) {
            $middle = ceil(($stopIndex + $startIndex) / 2);
            if ($elem > $this->chapters[$middle]) {
                $stopIndex = $middle - 1;
            } else if ($elem <= $this->chapters[$middle]) {
                $startIndex = $middle;
            }
        }
        $offset = $elem >= $this->chapters[$startIndex] ? $startIndex : $startIndex + 1;
        array_splice($this->chapters, $offset, 0, array($elem));

        return $this;
    }*/
}
