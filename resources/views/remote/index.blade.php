<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Remote Command Center</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Fira Code', 'Courier New', monospace;
        }

        body {
            background: #0f111a;
            color: #cfd4dc;
            min-height: 100vh;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #8ab4f8;
        }

        .card {
            background: #1b1f2a;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow:
                0 5px 20px rgba(0, 0, 0, .5);
        }

        form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        input {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #161b26;
            color: white;
            outline: none;
        }

        button {
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            color: white;
            transition: .3s;
        }

        .execute {
            background: #2563eb;
        }

        .execute:hover {
            background: #1d4ed8;
        }

        .clear {
            background: #dc2626;
            width: 100%;
            margin-top: 15px;
        }

        .clear:hover {
            background: #b91c1c;
        }

        h3 {
            margin-bottom: 15px;
            color: #8ab4f8;
        }

        .quick {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .quick button {
            background: #334155;
        }

        .quick button:hover {
            background: #475569;
        }

        pre {
            background: black;
            color: #00ff7f;
            padding: 20px;
            border-radius: 10px;
            overflow: auto;
            min-height: 100px;
        }

        .history-item {
            background: #161b26;
            padding: 15px;
            border-left: 4px solid #06b6d4;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .rerun-btn {
            background: #0891b2;
            padding: 8px 15px;
        }

        .rerun-btn:hover {
            background: #0e7490;
        }

        small {
            color: #94a3b8;
        }

        @media(max-width:768px) {

            form {
                flex-direction: column;
            }

            .history-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

        }
    </style>

</head>

<body>

    <div class="container">

        <h1>
            🚀 Remote Command Center
        </h1>


        <div class="card">

            <form
                method="POST"
                action="{{route('remote.execute')}}">

                @csrf

                <input
                    id="commandInput"
                    name="command"
                    placeholder="Enter command..."
                    required>

                <button
                    class="execute">

                    Execute

                </button>

            </form>


            <form
                method="POST"
                action="{{route('remote.clear')}}">

                @csrf

                <button
                    class="clear">

                    Clear Output

                </button>

            </form>

        </div>



        <div class="card">

            <h3>
                ⚡ Quick Commands
            </h3>

            <div class="quick">

                <button
                    type="button"
                    onclick="setCommand('pwd')">

                    pwd

                </button>

                <button
                    type="button"
                    onclick="setCommand('ls')">

                    ls

                </button>

                <button
                    type="button"
                    onclick="setCommand('whoami')">

                    whoami

                </button>

                <button
                    type="button"
                    onclick="setCommand('php artisan migrate')">

                    migrate

                </button>

                <button
                    type="button"
                    onclick="setCommand('php artisan cache:clear')">

                    cache clear

                </button>

                <button
                    type="button"
                    onclick="setCommand('php artisan optimize:clear')">

                    optimize clear

                </button>

            </div>

        </div>



        <div class="card">

            <h3>
                💻 Terminal Output
            </h3>

            <pre>

            {{$output ?: 'No output yet'}}

            </pre>

        </div>



        <div class="card">

            <h3>
                🕒 Recent History
            </h3>

            @forelse($history as $item)

            <div class="history-item">

                <div class="history-header">

                    <div>

                        <b>

                            {{$item->command}}

                        </b>

                        <br>

                        <small>

                            {{$item->created_at->diffForHumans()}}

                        </small>

                    </div>


                    <form
                        method="POST"
                        action="{{route('history.rerun',$item)}}">

                        @csrf

                        <button
                            class="rerun-btn">

                            Run Again

                        </button>

                    </form>

                </div>

            </div>

            @empty

            <p>
                No command history yet
            </p>

            @endforelse

        </div>

    </div>

    <script>
        function setCommand(command) {
            document
                .getElementById(
                    'commandInput'
                )
                .value = command;
        }
    </script>

</body>

</html>