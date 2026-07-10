<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Remote Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;600&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0f111a;
            color: #cfd4dc;
            font-family: 'Fira Code', 'Courier New', monospace;
            min-height: 100vh;
            padding: 20px;
        }
        .glass {
            background: rgba(27, 31, 42, 0.9);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }
        .terminal {
            background: #0a0c14;
            border-radius: 12px;
            padding: 20px;
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Fira Code', monospace;
            font-size: 14px;
            color: #00ff7f;
            white-space: pre-wrap;
            word-break: break-all;
            border: 1px solid rgba(0,255,127,0.1);
        }
        .terminal::-webkit-scrollbar { width: 8px; }
        .terminal::-webkit-scrollbar-track { background: #1b1f2a; border-radius: 10px; }
        .terminal::-webkit-scrollbar-thumb { background: #2563eb; border-radius: 10px; }
        .terminal .error { color: #ef4444; }
        .terminal .success { color: #22c55e; }
        .terminal .info { color: #eab308; }
        .command-input {
            background: #161b26;
            border: 1px solid #2d3748;
            border-radius: 10px;
            padding: 12px 16px;
            color: white;
            width: 100%;
            font-family: 'Fira Code', monospace;
            outline: none;
            transition: all 0.3s;
        }
        .command-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.2);
        }
        .btn-primary {
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }
        .btn-danger {
            background: #dc2626;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-danger:hover {
            background: #b91c1c;
        }
        .quick-btn {
            background: #1e293b;
            color: #94a3b8;
            padding: 8px 16px;
            border: 1px solid #334155;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 12px;
            font-family: 'Fira Code', monospace;
        }
        .quick-btn:hover {
            background: #334155;
            color: white;
        }
        .server-select {
            background: #161b26;
            border: 1px solid #2d3748;
            border-radius: 10px;
            padding: 10px 16px;
            color: white;
            width: 100%;
            outline: none;
            cursor: pointer;
            font-family: 'Fira Code', monospace;
        }
        .server-select:focus {
            border-color: #2563eb;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-active { background: #22c55e20; color: #22c55e; border: 1px solid #22c55e40; }
        .status-inactive { background: #ef444420; color: #ef4444; border: 1px solid #ef444440; }
        .history-item {
            padding: 12px 16px;
            border-left: 3px solid #2563eb;
            background: #161b26;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s;
        }
        .history-item:hover {
            background: #1e293b;
            transform: translateX(4px);
        }
        .history-item .cmd { color: #8ab4f8; font-weight: 600; }
        .history-item .time { color: #64748b; font-size: 11px; }
        .history-item .server-tag {
            background: #1e293b;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10px;
            color: #94a3b8;
        }
        .history-item .status-success { color: #22c55e; }
        .history-item .status-error { color: #ef4444; }
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 14px 24px;
            border-radius: 12px;
            background: rgba(27,31,42,0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            font-size: 14px;
            z-index: 9999;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            max-width: 400px;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { border-left: 4px solid #22c55e; }
        .toast.error { border-left: 4px solid #ef4444; }
        @media (max-width: 768px) {
            .terminal { font-size: 12px; max-height: 300px; }
            .quick-btn { font-size: 10px; padding: 6px 12px; }
        }
    </style>
</head>
<body>

    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-6" style="color: #8ab4f8;">🚀 Remote Command Center</h1>

        <div class="glass p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm text-slate-400 mb-1">Select Server</label>
                    <select id="serverSelect" class="server-select" onchange="switchServer(this.value)">
                        <option value="">-- Select Server --</option>
                        @foreach($servers as $server)
                            <option value="{{ $server->id }}" {{ $currentServer && $currentServer->id == $server->id ? 'selected' : '' }}>
                                {{ $server->name }} ({{ $server->host }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    @if($currentServer)
                        <span class="status-badge status-active">🟢 Connected</span>
                        <span class="text-sm text-slate-400">{{ $currentServer->name }}</span>
                    @else
                        <span class="status-badge status-inactive">⛔ No Server Selected</span>
                    @endif
                    <button onclick="testConnection()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        🔌 Test
                    </button>
                </div>
            </div>
        </div>

        <div class="glass p-6 mb-6">
            <form id="commandForm" class="flex flex-col md:flex-row gap-3">
                @csrf
                <input type="hidden" name="server_id" id="serverIdInput" value="{{ $currentServer?->id }}">
                <input type="text" name="command" id="commandInput" placeholder="Enter command..." 
                    class="command-input flex-1" autofocus>
                <button type="submit" id="executeBtn" class="btn-primary">
                    ⚡ Execute
                </button>
                <button type="button" onclick="clearOutput()" class="btn-danger">
                    🗑️ Clear
                </button>
            </form>
        </div>

        <div class="glass p-6 mb-6">
            <h3 class="text-lg font-semibold mb-3 text-slate-300">⚡ Quick Commands</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($quickCommands as $cmd => $desc)
                    <button type="button" class="quick-btn" onclick="setCommand('{{ $cmd }}')" title="{{ $desc }}">
                        {{ $cmd }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="glass p-6 mb-6">
            <h3 class="text-lg font-semibold mb-3 text-slate-300">💻 Terminal Output</h3>
            <div class="terminal" id="terminalOutput">
                @if($output)
                    {!! nl2br(e($output)) !!}
                @else
                    <span class="info">$</span> Ready for commands...
                @endif
            </div>
        </div>

        <div class="glass p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-slate-300">🕒 Command History</h3>
                <button onclick="refreshHistory()" class="text-sm text-indigo-400 hover:text-indigo-300 transition">
                    🔄 Refresh
                </button>
            </div>
            <div id="historyContainer">
                @forelse($history as $item)
                    <div class="history-item">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="cmd">$ {{ $item->command }}</span>
                                <div class="text-xs text-slate-400 mt-1">
                                    <span class="server-tag">{{ $item->server?->name ?? 'Unknown' }}</span>
                                    <span class="time">{{ $item->created_at->diffForHumans() }}</span>
                                    @if($item->exit_code === 0)
                                        <span class="status-success">✅</span>
                                    @else
                                        <span class="status-error">❌</span>
                                    @endif
                                </div>
                                @if($item->output && strlen($item->output) < 100)
                                    <div class="text-xs text-slate-500 mt-1 truncate">{{ $item->output }}</div>
                                @endif
                            </div>
                            <button onclick="rerunCommand({{ $item->id }})" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg text-xs transition">
                                🔄 Rerun
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-4">No command history yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        const terminal = document.getElementById('terminalOutput');
        const commandInput = document.getElementById('commandInput');
        const executeBtn = document.getElementById('executeBtn');
        const serverSelect = document.getElementById('serverSelect');
        const serverIdInput = document.getElementById('serverIdInput');

        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type;
            setTimeout(() => toast.classList.add('show'), 50);
            clearTimeout(toast._timer);
            toast._timer = setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        function setCommand(cmd) {
            commandInput.value = cmd;
            commandInput.focus();
        }

        function switchServer(serverId) {
            if (!serverId) return;
            serverIdInput.value = serverId;
            
            $.ajax({
                url: '{{ route("remote.switch") }}',
                type: 'POST',
                data: {
                    server_id: serverId,
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    showToast('✅ Server switched successfully', 'success');
                    location.reload();
                },
                error: function() {
                    showToast('❌ Failed to switch server', 'error');
                }
            });
        }

        function testConnection() {
            const serverId = serverIdInput.value;
            if (!serverId) {
                showToast('❌ Please select a server first', 'error');
                return;
            }

            showToast('🔌 Testing connection...', 'info');

            $.ajax({
                url: '/remote/test/' + serverId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        showToast('✅ ' + response.message, 'success');
                    } else {
                        showToast('❌ ' + response.message, 'error');
                    }
                },
                error: function() {
                    showToast('❌ Connection test failed', 'error');
                }
            });
        }

        $('#commandForm').on('submit', function(e) {
            e.preventDefault();

            const command = commandInput.value.trim();
            if (!command) {
                showToast('❌ Please enter a command', 'error');
                return;
            }

            const serverId = serverIdInput.value;
            if (!serverId) {
                showToast('❌ Please select a server', 'error');
                return;
            }

            executeBtn.disabled = true;
            executeBtn.innerHTML = '⏳ Running...';
            terminal.innerHTML = '<span class="info">⏳ Executing command...</span>';

            $.ajax({
                url: '{{ route("remote.execute") }}',
                type: 'POST',
                data: {
                    command: command,
                    server_id: serverId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const output = response.output || '✅ Command executed successfully (no output)';
                        terminal.innerHTML = output.replace(/\n/g, '<br>');
                        showToast('✅ Command executed on ' + response.server_name, 'success');
                        refreshHistory();
                    } else {
                        terminal.innerHTML = '<span class="error">❌ ' + (response.output || 'Command failed') + '</span>';
                        showToast('❌ Command failed', 'error');
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.output || 'Unknown error occurred';
                    terminal.innerHTML = '<span class="error">❌ Error: ' + error + '</span>';
                    showToast('❌ Error executing command', 'error');
                },
                complete: function() {
                    executeBtn.disabled = false;
                    executeBtn.innerHTML = '⚡ Execute';
                    commandInput.value = '';
                    commandInput.focus();
                }
            });
        });

        function clearOutput() {
            $.ajax({
                url: '{{ route("remote.clear") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    terminal.innerHTML = '<span class="info">$</span> Ready for commands...';
                    showToast('🗑️ Output cleared', 'info');
                }
            });
        }

        function rerunCommand(id) {
            showToast('🔄 Rerunning command...', 'info');
            
            $.ajax({
                url: '/remote/rerun/' + id,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        terminal.innerHTML = response.output.replace(/\n/g, '<br>');
                        refreshHistory();
                    } else {
                        terminal.innerHTML = '<span class="error">❌ ' + (response.output || 'Rerun failed') + '</span>';
                        showToast('❌ Rerun failed', 'error');
                    }
                },
                error: function() {
                    showToast('❌ Failed to rerun command', 'error');
                }
            });
        }

        function refreshHistory() {
            $.ajax({
                url: '{{ route("remote.history") }}',
                type: 'GET',
                success: function(data) {
                    const container = document.getElementById('historyContainer');
                    if (data.length === 0) {
                        container.innerHTML = '<p class="text-slate-500 text-center py-4">No command history yet</p>';
                        return;
                    }

                    let html = '';
                    data.forEach(item => {
                        const serverName = item.server ? item.server.name : 'Unknown';
                        const time = new Date(item.created_at).toLocaleString();
                        const status = item.exit_code === 0 ? '✅' : '❌';
                        const statusClass = item.exit_code === 0 ? 'status-success' : 'status-error';
                        
                        html += `
                            <div class="history-item">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="cmd">$ ${item.command}</span>
                                        <div class="text-xs text-slate-400 mt-1">
                                            <span class="server-tag">${serverName}</span>
                                            <span class="time">${time}</span>
                                            <span class="${statusClass}">${status}</span>
                                        </div>
                                        ${item.output && item.output.length < 100 ? `<div class="text-xs text-slate-500 mt-1 truncate">${item.output}</div>` : ''}
                                    </div>
                                    <button onclick="rerunCommand(${item.id})" 
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg text-xs transition">
                                        🔄 Rerun
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                },
                error: function() {
                    showToast('❌ Failed to load history', 'error');
                }
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                commandInput.focus();
                showToast('⌨️ Command input focused', 'info');
            }
            if (e.key === 'Enter' && document.activeElement === commandInput) {
                document.getElementById('commandForm').dispatchEvent(new Event('submit'));
            }
        });

        // Auto-focus on load
        commandInput.focus();

        // Refresh history every 30 seconds
        setInterval(refreshHistory, 30000);
    </script>

</body>
</html>