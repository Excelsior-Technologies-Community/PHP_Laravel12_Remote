<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RemoteService;
use App\Models\CommandHistory;

class RemoteController extends Controller
{
    protected $remote;

    public function __construct(RemoteService $remote)
    {
        $this->remote = $remote;
    }

    public function index()
    {
        // Existing session output
        $output = session(
            'remote_output',
            ''
        );

        // NEW:
        // Get latest 10 command records
        $history = CommandHistory::latest()
                    ->take(10)
                    ->get();

        return view(
            'remote.index',
            compact(
                'output',
                'history'
            )
        );
    }

    public function execute(Request $request)
    {
        $request->validate([
            'command'=>'required|string',
        ]);

        $command =
            $request->input(
                'command'
            );

        $output =
            $this->remote
                 ->run($command);

        // NEW:
        // Save executed command
        CommandHistory::create([

            'command'=>$command,

            'output'=>$output

        ]);

        // Existing logic
        session([
            'remote_output'=>$output
        ]);

        return redirect()
                ->back();
    }

    public function clearOutput()
    {
        session()->forget(
            'remote_output'
        );

        return redirect()
                ->back();
    }

    // NEW:
    // One click rerun support
    public function rerun(
        CommandHistory $history
    )
    {
        $output =
            $this->remote
                 ->run(
                    $history->command
                 );

        session([
            'remote_output'=>$output
        ]);

        return back();
    }
}