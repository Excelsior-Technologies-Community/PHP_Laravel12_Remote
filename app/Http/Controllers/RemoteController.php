<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RemoteService;
use App\Models\CommandHistory;
use App\Models\RemoteServer;
use Illuminate\Support\Facades\Session;

class RemoteController extends Controller
{
    protected $remote;

    public function __construct(RemoteService $remote)
    {
        $this->remote = $remote;
    }

    public function index(Request $request)
    {
        $serverId = $request->session()->get('remote_server_id');
        
        try {
            if ($serverId) {
                $this->remote->setServer($serverId);
            }
            $currentServer = $this->remote->getServer();
        } catch (\Exception $e) {
            $currentServer = null;
        }

        $servers = RemoteServer::where('is_active', true)->get();
        
        // Get output from session or default
        $output = Session::get('remote_output', '');
        
        // Get history
        $history = CommandHistory::with('server')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $quickCommands = RemoteService::getAvailableCommands();

        return view('remote.index', compact(
            'servers',
            'currentServer',
            'output',
            'history',
            'quickCommands'
        ));
    }

    public function execute(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
            'server_id' => 'nullable|exists:remote_servers,id'
        ]);

        $serverId = $request->server_id;
        $command = $request->command;

        try {
            if ($serverId) {
                $this->remote->setServer($serverId);
                Session::put('remote_server_id', $serverId);
            }

            // Execute command
            $output = $this->remote->run($command);
            $server = $this->remote->getServer();

            // Store output in session
            Session::put('remote_output', $output);

            // Get updated history
            $history = CommandHistory::with('server')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'output' => $output,
                'server_name' => $server ? $server->name : 'Unknown',
                'command' => $command,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'output' => '❌ Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearOutput()
    {
        Session::forget('remote_output');
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }

    public function rerun($id)
    {
        $history = CommandHistory::with('server')->findOrFail($id);
        
        try {
            if ($history->server) {
                $this->remote->setServer($history->server->id);
            }
            
            $output = $this->remote->run($history->command);
            Session::put('remote_output', $output);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'output' => $output
                ]);
            }

            return redirect()->back();

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'output' => '❌ Error: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function switchServer(Request $request)
    {
        $request->validate([
            'server_id' => 'required|exists:remote_servers,id'
        ]);

        Session::put('remote_server_id', $request->server_id);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }

    public function testConnection($id)
    {
        $server = RemoteServer::findOrFail($id);
        
        try {
            $this->remote->setServer($id);
            $result = $this->remote->testConnection();
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getHistory()
    {
        try {
            $history = CommandHistory::with('server')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
            
            return response()->json($history);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}