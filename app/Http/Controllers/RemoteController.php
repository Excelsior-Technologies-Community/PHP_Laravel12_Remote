<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RemoteService;

class RemoteController extends Controller
{
    protected $remote;

    public function __construct(RemoteService $remote)
    {
        $this->remote = $remote;
    }

    public function index()
    {
        $output = session('remote_output', '');
        return view('remote.index', compact('output'));
    }

    public function execute(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
        ]);

        $command = $request->input('command');

        $output = $this->remote->run($command);

        // Store output in session
        session(['remote_output' => $output]);

        return redirect()->back();
    }

    public function clearOutput()
    {
        session()->forget('remote_output');
        return redirect()->back();
    }
}