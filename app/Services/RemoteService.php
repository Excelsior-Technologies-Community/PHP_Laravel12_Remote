<?php

namespace App\Services;

use App\Models\RemoteServer;
use App\Models\CommandHistory;
use Illuminate\Support\Facades\Log;

class RemoteService
{
    protected $server;
    protected $projectPath;

    public function __construct($serverId = null)
    {
        $this->projectPath = base_path();

        if ($serverId) {
            $this->server = RemoteServer::find($serverId);
        } else {
            $this->server = RemoteServer::getDefault();
        }

        if (!$this->server) {
            $this->server = RemoteServer::where('is_active', true)->first();
            
            if (!$this->server) {
                $this->server = RemoteServer::create([
                    'name' => 'Local Server',
                    'host' => '127.0.0.1',
                    'port' => 22,
                    'username' => 'root',
                    'auth_type' => 'password',
                    'is_active' => true,
                    'is_default' => true
                ]);
                Log::info('Created default local server for remote commands');
            }
        }
    }

    public function setServer($serverId)
    {
        $this->server = RemoteServer::findOrFail($serverId);
        return $this;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function run(string $command): string
    {
        try {
            $output = [];
            $returnVar = 0;
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

            // Build command
            if ($isWindows) {
                $fullCommand = "cd /d \"{$this->projectPath}\" && {$command} 2>&1";
            } else {
                $fullCommand = "cd {$this->projectPath} && {$command} 2>&1";
            }

            Log::info('Executing command', [
                'command' => $command,
                'server' => $this->server->name ?? 'local',
                'path' => $this->projectPath
            ]);

            // Execute command
            exec($fullCommand, $output, $returnVar);

            // Process output
            $result = $this->formatOutput($output, $returnVar, $command);

            // Save to history
            $this->saveHistory($command, $result, $returnVar);

            return $result;

        } catch (\Exception $e) {
            $errorMsg = '❌ Error: ' . $e->getMessage();
            Log::error('Remote command exception', [
                'command' => $command,
                'error' => $e->getMessage()
            ]);
            
            $this->saveHistory($command, $errorMsg, 1);
            return $errorMsg;
        }
    }

    protected function formatOutput(array $output, int $returnVar, string $command): string
    {
        if (empty($output)) {
            return $returnVar === 0 
                ? '✅ Command executed successfully (no output)' 
                : '❌ Command failed with exit code: ' . $returnVar;
        }

        $result = implode("\n", $output);
        
        // Add color coding based on exit code
        if ($returnVar !== 0) {
            $result = "⚠️ Exit Code: {$returnVar}\n" . $result;
        }

        return $result;
    }

    protected function saveHistory(string $command, string $output, int $exitCode): void
    {
        try {
            CommandHistory::create([
                'server_id' => $this->server ? $this->server->id : null,
                'command' => $command,
                'output' => $output,
                'exit_code' => $exitCode
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to save command history', [
                'command' => $command,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function runMultiple(array $commands): array
    {
        $results = [];
        foreach ($commands as $command) {
            $results[$command] = $this->run($command);
        }
        return $results;
    }

    public function testConnection(): array
    {
        try {
            $result = $this->run('echo "Connection successful"');
            return [
                'success' => true,
                'message' => trim($result)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public static function getAvailableCommands(): array
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        $commands = [
            'whoami' => 'Current user',
            'pwd' => 'Print working directory',
            'php artisan migrate' => 'Run migrations',
            'php artisan cache:clear' => 'Clear cache',
            'php artisan config:clear' => 'Clear config',
            'php artisan route:clear' => 'Clear routes',
            'php artisan view:clear' => 'Clear views',
            'php artisan optimize:clear' => 'Optimize clear',
            'php artisan route:list' => 'List all routes',
            'php artisan tinker' => 'Open Tinker',
        ];

        if ($isWindows) {
            $commands['dir'] = 'List directory (Windows)';
            $commands['systeminfo'] = 'System info (Windows)';
        } else {
            $commands['ls'] = 'List directory (Linux)';
            $commands['df -h'] = 'Disk space usage (Linux)';
            $commands['free -m'] = 'Memory usage (Linux)';
            $commands['uptime'] = 'System uptime (Linux)';
        }

        return $commands;
    }

    public function getHistory($limit = 20)
    {
        return CommandHistory::with('server')
            ->where('server_id', $this->server ? $this->server->id : null)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function clearHistory()
    {
        return CommandHistory::where('server_id', $this->server ? $this->server->id : null)->delete();
    }
}