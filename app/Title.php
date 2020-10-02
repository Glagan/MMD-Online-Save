<?php namespace App;

use App\Chapter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Title extends Model
{
	use HasFactory;

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
		$hasChapter = Chapter::where('title_id', '=', $this->id)
			->where('value', '=', $chapter)
			->first();
		return ($hasChapter != null);
	}

	public function sortedChapters($order = 'ASC')
	{
		return $this->chapters()
			->orderBy(DB::raw('value*1'), $order);
	}

	public function insertChapter($chapterValue)
	{
		$chapter = new Chapter;
		$chapter->title_id = $this->id;
		$chapter->value = $chapterValue;
		$chapter->save();

		return $this;
	}

	public static function chapterRange($title, $from, $to)
	{
		$chapters = [];
		for ($i = $from; $i <= $to; $i++) {
			$chapters[] = [
				'title_id' => $title->id,
				'value' => $i,
			];
		}
		return $chapters;
	}

	public function addChapterRange($from, $to)
	{
		Chapter::insert($this->chapterRange($this, $from, $to));
		return $this;
	}
}
