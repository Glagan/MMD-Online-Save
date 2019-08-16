<?php

namespace App\Http\Controllers;

use App\User;
use App\Title;
use App\Chapter;
use App\HistoryEntry;
use App\HistoryTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('token_auth', [
            'except' => [
                'register',
                'login',
                'token',
                'showToken',
                'refreshToken',
                'update',
                'delete'
            ]
        ]);
        $this->middleware('credentials_auth', [
            'except' => [
                'register',
                'show',
                'showOptions',
                'updateOptions',
                'exportAll',
                'importAll'
            ]
        ]);
    }

    /**
     * Register a new App\User
     * Require the field username, password.
     * Optional fields: options
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:users',
            'password' => 'required|min:10',
            'options' => 'array'
        ]);

        $user = User::make([
            'username' => $request->input('username'),
            'options' => \json_encode($request->input('options', ''))
        ]);
        $user->password = Hash::make($request->input('password'));
        $user->generateToken();
        $user->save();

        return response()->json([
            'status' => 'Account created',
            'token' => $user->token
        ], 201);
    }

    /**
     * Login an user with the Middleware and display the token
     */
    public function login(Request $request) {
        return response()->json([
            'status' => 'Correct credentials',
            'token' => Auth::user()->token,
        ], 200);
    }

    /**
     * Return the token of an App\User
     */
    public function showToken()
    {
        // Return token
        return response()->json([
            'token' => Auth::user()->token,
        ], 200);
    }

    /**
     * Generate a new token for an App\User
     */
    public function refreshToken()
    {
        Auth::user()->generateToken()->save();

        // Return token
        return response()->json([
            'status' => 'Token updated',
            'token' => Auth::user()->token,
        ], 200);
    }

    /**
     * Update an App\User
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'password' => 'min:10'
        ]);

        // Update the only 2 editable fields
        if ($request->has('password')) {
            Auth::user()->password = Hash::make($request->input('password'));
        }
        if ($request->has('options')) {
            Auth::user()->options = \json_encode($request->input('options'));
        }
        Auth::user()->generateToken();
        // Save
        Auth::user()->save();

        return response()->json([
            'status' => 'User updated',
            'options' => \json_decode(Auth::user()->options, true),
            'token' => Auth::user()->token
        ], 200);
    }

    /**
     * Delete and App\User
     * All App\Title will also delete on cascade
     */
    public function delete()
    {
        Auth::user()->delete();

        return response()->json([
            'status' => 'User deleted'
        ], 200);
    }

    /**
     * Display informations about an App\User
     * {
     *  username
     *  token
     *  options
     *  last_sync
     *  creation_date
     *  last_update
     * }
     */
    public function show()
    {
        Auth::user()->options = \json_decode(Auth::user()->options, true);
        return response()->json(Auth::user(), 200);
    }

    /**
     * Display the saved options of an App\User
     */
    public function showOptions()
    {
        return response()->json([
            'options' => \json_decode(Auth::user()->options, true)
        ], 200);
    }

    /**
     * Update the options of an App\User
     */
    public function updateOptions(Request $request)
    {
        $this->validate($request, [
            'options' => 'array'
        ]);
        Auth::user()->options = \json_encode($request->input('options'));
        Auth::user()->save();

        return response()->json([
            'status' => 'Options saved',
            'options' => $request->input('options')
        ], 200);
    }

    /**
     * Export all user data
     * {
     *  options
     *  titles[md_id] {
     *    md_id
     *    mal_id
     *    last
     *    chapters { progress }
     *  }
     *  history {
     *    list { md_id }
     *    titles[md_id] {
     *      name
     *      md_id
     *      progress
     *      chapter
     *    }
     *  }
     * }
     */
    public function exportAll()
    {
        $data = [
            'options' => \json_decode(Auth::user()->options),
            'titles' => [],
            'history' => [
                'list' => [],
                'titles' => []
            ]
        ];
        // Titles
        foreach (Auth::user()->titles as $title) {
            $title->chapters = $title->sortedChapters('ASC')->pluck('value');
            $data['titles'][] = $title;
        }
        // History
        $data['history']['list'] = Auth::user()->historyEntries()->pluck('md_id');
        foreach (Auth::user()->historyTitles()->get() as $title) {
            $data['history']['titles'][] = $title;
        }
        return response()->json($data, 200);
    }

    /**
     * Import all user data
     * {
     *  options: string
     *  titles {
     *    mal
     *    last
     *    chapters { progress }
     *  }
     *  history {
     *    list { md_id }
     *    titles {
     *      name
     *      md_id
     *      progress
     *      chapter
     *    }
     *  }
     * }
     */
    public function importAll(Request $request)
    {
        $this->validate($request, [
            'options' => 'array',
            'titles' => 'array',
            'titles.*' => 'array',
            'titles.*.mal' => 'integer',
            'titles.*.last' => 'numeric',
            'titles.*.chapters' => 'array',
            'titles.*.chapters.*' => 'numeric',
            'history' => 'array',
            'history.*' => 'array',
            'history.list.*' => 'integer',
            'history.titles.*' => 'array',
            'history.titles.*.name' => 'required|string',
            'history.titles.*.md_id' => 'required|integer',
            'history.titles.*.progress' => 'required|numeric',
            'history.titles.*.chapter' => 'required|integer'
        ]);
        $state = [
            'options' => 'Options not updated',
            'titles' => 0,
            'history' => 'History not updated'
        ];

        $options = false;
        if ($request->has('options')) {
            $state['options'] = 'Options updated';
            $options = $request->input('options');
            Auth::user()->options = \json_encode($request->input('options'));
            Auth::user()->save();
        }
        if (!$options) {
            $options = [];
            if (!isset($options['saveAllOpened']) || !filter_var($options['saveAllOpened'], FILTER_VALIDATE_BOOLEAN)) {
                $options['saveAllOpened'] = true;
            }
            if (!isset($options['maxChapterSaved']) || !filter_var($options['maxChapterSaved'], FILTER_VALIDATE_INT)) {
                $options['maxChapterSaved'] = true;
            }
        }

        // Delete all old titles
        Title::where('user_id', Auth::user()->id)->delete();
        if ($request->has('titles')) {
            foreach ($request->input('titles', []) as $key => $value) {
                $state['titles']++;
                // Make a new title
                $newTitle = [
                    'user_id' => Auth::user()->id,
                    'md_id' => $key,
                    'mal_id' => $request->input('titles.' . $key . '.mal', 0),
                    'last' => $request->input('titles.' . $key . '.last', 0),
                ];
                $chapterList = [ 'md_id' => $key, 'list' => [] ];
                if ($options['saveAllOpened']) {
                    $hasChapters = (isset($value['chapters']) && is_array($value['chapters']));
                    if ($hasChapters) {
                        $chapterList['list'] = $value['chapters'];
                    } else if ($newTitle['last'] > 0) {
                        $chapterList['generate'] = true;
                    }
                }
                $titles[] = $newTitle;
                $chapters[] = $chapterList;
            }
            Title::insert($titles);
            $titles = Title::where('user_id', Auth::user()->id)->get();
            // Chapters
            if ($options['saveAllOpened']) {
                $allChapters = [];
                foreach ($chapters as $titleChapters) {
                    $currentTitle = false;
                    foreach ($titles as $tmpTitle) {
                        if ($tmpTitle->md_id == $titleChapters['md_id']) {
                            $currentTitle = $tmpTitle;
                            break;
                        }
                    }
                    if (isset($titleChapters['generate'])) {
                        $start = max($newTitle['last'] - $options['maxChapterSaved'], 0);
                        $chapters = Title::chapterRange($currentTitle, $start, $newTitle['last']);
                        foreach ($chapters as $chapter) {
                            $allChapters[] = $chapter;
                        }
                    } else {
                        foreach ($titleChapters['list'] as $chapter) {
                            $allChapters[] = [
                                'title_id' => $currentTitle->id,
                                'value' => $chapter
                            ];
                        }
                    }
                }
            }
            Chapter::insert($allChapters);
        }

        if ($request->has('history')) {
            $state['history'] = 'History updated';
            // Entries
            Auth::user()->historyEntries()->delete();
            $entries = [];
            foreach ($request->input('history.list', []) as $historyEntry) {
                $entries[] = [
                    'md_id' => $historyEntry,
                    'user_id' => Auth::user()->id
                ];
            }
            HistoryEntry::insert($entries);
            // Titles
            Auth::user()->historyTitles()->delete();
            $titles = [];
            foreach ($request->input('history.titles', []) as $key => $historyTitle) {
                $titles[] = [
                    'name' => $historyTitle['name'],
                    'md_id' => $historyTitle['md_id'],
                    'progress' => $historyTitle['progress'],
                    'chapter' => $historyTitle['chapter'],
                    'user_id' => Auth::user()->id
                ];
            }
            HistoryTitle::insert($titles);
        }

        return response()->json([
            'status' => 'Data imported',
            'options' => $state['options'],
            'titles' => $state['titles'] . ' title(s) imported',
            'history' => $state['history']
        ], 200);
    }
}