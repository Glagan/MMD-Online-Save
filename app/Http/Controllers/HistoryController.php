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
            'history.titles.*.progress' => 'required|numeric',
            'history.titles.*.chapter_id' => 'required|numeric'
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
                'progress' => $value['progress'],
                'chapter_id' => $value['chapter_id']
            ]);
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
            $history[$value->md_id] = $value;
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
