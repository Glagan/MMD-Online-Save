<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\HistoryEntry;
use App\HistoryTitle;

class HistoryController extends Controller
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

    public function updateAll(Request $request)
    {
        // Delete all old titles
        HistoryEntry::where('user_id', Auth::user()->id)->delete();
        HistoryTitle::where('user_id', Auth::user()->id)->delete();

        $this->validate($request, [
            'history' => 'array',
            'history.list' => 'array',
            'history.list.*' => 'numeric',
            'history.titles' => 'array',
            'history.titles.*' => 'array',
            'history.titles.*.name' => 'required|string',
            'history.titles.*.md_id' => 'required|integer',
            'history.titles.*.progress' => 'required',
            'history.titles.*.chapter' => 'required|numeric'
        ]);

        // Insert list
        $total = 0;
        foreach ($request->input('history.list', []) as $value) {
            $total++;

            // Make a new HistoryEntry
            $historyEntry = HistoryEntry::make([
                'md_id' => $value,
            ]);
            $historyEntry->user_id = Auth::user()->id;
            // Done App\Title
            $historyEntry->save();
        }

        // Insert titles
        foreach ($request->input('history.titles', []) as $value) {
            $total++;

            // Make a new HistoryEntry
            $historyTitle = HistoryTitle::make([
                'name' => $value['name'],
                'md_id' => $value['md_id'],
                'chapter' => $value['chapter']
            ]);
            if (is_array($value['progress'])) {
                if (isset($value['progress']['chapter'])) {
                    $historyTitle->progress = $value['progress']['chapter'];
                }
                if (isset($value['progress']['volume'])) {
                    $historyTitle->volume = $value['progress']['volume'];
                }
            } else {
                $historyTitle->progress = $value['progress'];
            }
            $historyTitle->user_id = Auth::user()->id;
            // Done App\Title
            $historyTitle->save();
        }
        return response()->json([
            'status' => 'History updated',
            'inserted' => $total
        ], 200);
    }

    public function showAll(Request $request)
    {
        $history = [ 'list' => Auth::user()->historyEntries() ];
        $historyTitles = Auth::user()->historyTitles();
        foreach ($historyTitles as $value) {
            $history[$value->md_id] = [
                'name' => $value->name,
                'md_id' => $value->md_id,
                'chapter' => $value->chapter,
                'progress' => [
                    'volume' => $value->volume,
                    'chapter' => $value->progress
                ]
            ];
        }
        return response()->json([
            'history' => $history
        ], 200);
    }

    public function deleteAll(Request $request)
    {
        HistoryEntry::where('user_id', Auth::user()->id)->delete();
        HistoryTitle::where('user_id', Auth::user()->id)->delete();

        return response()->json([
            'status' => 'History deleted'
        ], 200);
    }
}
