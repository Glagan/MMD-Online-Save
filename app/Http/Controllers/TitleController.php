<?php

namespace App\Http\Controllers;

use App\Title;
use App\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\HistoryEntry;
use App\HistoryTitle;

class TitleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('token_auth');
    }

    public function showSingle(Request $request, $mangaDexId)
    {
        $title = Auth::user()->titles()->where('md_id', $mangaDexId)->first();
        if ($title == null) {
            return response()->json([
                'status' => 'No saved title for this id'
            ], 404);
        }

        $title->chapters = $title->sortedChapters('DESC')->pluck('value');
        return $title;
    }

    /**
     * {
     *  options
     *  mal
     *  last
     *  chapters { progress }
     *  title_name
     *  chapter_id
     * }
     */
    public function updateSingle(Request $request, $mangaDexId)
    {
        $this->validate($request, [
            'options' => 'array',
            'options.saveAllOpened' => 'boolean',
            'options.maxChapterSaved' => 'integer',
            'options.updateHistoryPage' => 'boolean',
            'mal' => 'integer',
            'last' => 'required|numeric',
            'chapters' => 'array',
            'chapters.*' => 'numeric',
            'title_name' => 'required_if:options.updateHistoryPage,true',
            'chapter_id' => 'required_if:options.updateHistoryPage,true|numeric'
        ]);

        $title = Title::firstOrNew([
            'user_id' => Auth::user()->id,
            'md_id' => $mangaDexId,
        ]);
        $options = Auth::user()->getOptions($request);

        // Update Informations
        $created = ($title->id == null);
        if ($created) {
            $title->user_id = Auth::user()->id;
            $title->md_id = $mangaDexId;
            $title->mal_id = $request->input('mal', 0);
            $title->last = $request->input('last', 0);
        } else {
            // Only update if needed when there is already a title
            if ($title->mal_id == 0 && $request->input('mal', 0) > 0) {
                $title->mal_id = $request->input('mal', 0);
            }
            if ($title->last < $request->input('last', 0)) {
                $title->last = $request->input('last', 0);
            }
        }
        $title->save();

        // Update chapters list
        if ($options['saveAllOpened'] && $title->last > 0) {
            if ($request->has('chapters')) {
                if (!$created) {
                    $title->chapters()->delete();
                }

                // Construct all chapters to insert them all at once
                $allChapters = \array_map(function($element) use ($title) {
                    return [
                        'title_id' => $title->id,
                        'value' => $element
                    ];
                }, $request->input('chapters'));
                Chapter::insert($allChapters);
            } else {
                if ($created) {
                    $start = \max($title->last - $options['maxChapterSaved'], 0);
                    $title->addChapterRange($start, $title->last);
                } else if (!$title->hasChapter($title->last)) {
                    $title->insertChapter($title->last);
                }
            }
        }

        // Delete chapters over the limit
        if (!$created) {
            if (($count = $title->chapters()->count()) > $options['maxChapterSaved']) {
                $offset = $count - $options['maxChapterSaved'];
                // Delete the last X chapters
                $title->sortedChapters('ASC')->limit($offset)->delete();
            }
        }

        // Save History
        if ($options['updateHistoryPage']) {
            // Entry in the list
            $historyEntry = HistoryEntry::where([
                [ 'user_id', Auth::user()->id ],
                [ 'md_id', $mangaDexId ]
            ])->first();
            $create = true;
            if ($historyEntry) {
                $above = HistoryEntry::where([
                    [ 'id', '>', $historyEntry->id ],
                    [ 'user_id', Auth::user()->id ]
                ])->count();
                if ($above > 0) {
                    $historyEntry->delete();
                } else {
                    $create = false;
                }
            }
            if ($create) {
                $historyEntry = HistoryEntry::make([
                    'md_id' => $mangaDexId
                ]);
                $historyEntry->user_id = Auth::user()->id;
                $historyEntry->save();
            }

            // Title
            $historyTitle = HistoryTitle::where([
                [ 'user_id', Auth::user()->id ],
                [ 'md_id', $mangaDexId ]
            ])->first();
            if (!$historyTitle) {
                $historyTitle = HistoryTitle::make([
                    'name' => $request->input('title_name'),
                    'md_id' => $mangaDexId
                ]);
                $historyTitle->user_id = Auth::user()->id;
            }
            $historyTitle->progress = $title->last;
            $historyTitle->chapter_id = $request->input('chapter_id');
            $historyTitle->save();
        }

        // Done
        return response()->json([
            'status' => 'Title #' . $mangaDexId . ' ' . (($created) ? 'added' : 'updated') . '.',
            'last' => $title->last,
        ], 200);
    }

    public function deleteSingle(Request $request, $mangaDexId)
    {
        $title = Title::where('user_id', '=', Auth::user()->id)
            ->where('md_id', '=', $mangaDexId)->first();

        // Delete if exist
        if ($title) {
            $title->delete();

            return response()->json([
                'status' => 'Title #' . $mangaDexId . ' deleted'
            ], 200);
        }

        return response()->json([
            'status' => 'No title with the id #' . $mangaDexId
        ], 404);
    }

    public function showAll(Request $request)
    {
        $titles = Auth::user()->titles;
        // Add chapters
        foreach ($titles as $title) {
            $title->chapters = $title->sortedChapters('ASC')->pluck('value');
        }

        return response()->json([
            'titles' => $titles
        ], 200);
    }

    public function updateAll(Request $request)
    {
        $this->validate($request, [
            'options' => 'array',
            'options.saveAllOpened' => 'boolean',
            'options.maxChapterSaved' => 'integer',
            'titles' => 'array',
            'titles.*' => 'array',
            'titles.*.mal' => 'required|integer',
            'titles.*.last' => 'required|numeric',
            'titles.*.chapters' => 'array',
            'titles.*.chapters.*' => 'numeric'
        ]);
        $options = Auth::user()->getOptions($request);

        // Delete all old titles
        Title::where('user_id', Auth::user()->id)->delete();

        // Insert new ones
        $total = 0;
        foreach ($request->input('titles', []) as $key => $value) {
            $total++;

            // Make a new title
            $title = Title::make([
                'md_id' => $key,
                'mal_id' => $request->input('titles.' . $key . '.mal'),
                'last' => $request->input('titles.' . $key . '.last'),
            ]);
            $title->user_id = Auth::user()->id;
            // Done App\Title
            $title->save();

            // Update chapters list
            if ($options['saveAllOpened']) {
                $hasChapters = (array_key_exists('chapters', $value) && count($value['chapters']) > 0);
                if ($hasChapters) {
                    // Construct all chapters to insert them all at once
                    $allChapters = array_map(function($element) use ($title) {
                        return [
                            'title_id' => $title->id,
                            'value' => $element
                        ];
                    }, $value['chapters']);
                    Chapter::insert($allChapters);
                } else if ($title->last > 0) {
                    $start = max($title->last - $options['maxChapterSaved'], 0);
                    $title->addChapterRange($start, $title->last);
                }
            }
        }

        return response()->json([
            'status' => $total . ' title(s) inserted',
            'inserted' => $total,
        ], 200);
    }

    public function deleteAll(Request $request)
    {
        $deleted = Title::where('user_id', '=', Auth::user()->id)->delete();

        return response()->json([
            'status' => 'Deleted ' . $deleted . ' title(s)'
        ], 200);
    }
}
