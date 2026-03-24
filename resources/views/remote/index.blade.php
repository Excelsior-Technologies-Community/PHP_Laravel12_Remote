<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remote Command Executor</title>
    <style>
        /* Base Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Fira Code', 'Courier New', monospace;
        }

        body {
            background-color: #0f111a;
            /* Very dark background */
            color: #cfd4dc;
            /* Soft gray text */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: #1b1f2a;
            /* Dark card */
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.6);
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
            color: #8ab4f8;
            /* Soft professional blue */
        }

        form {
            display: flex;
            margin-bottom: 15px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px 15px;
            font-size: 16px;
            border: 1px solid #2c313e;
            border-right: none;
            border-radius: 6px 0 0 6px;
            background-color: #161b26;
            color: #cfd4dc;
            outline: none;
        }

        input[type="text"]::placeholder {
            color: #7a7f8c;
        }

        button {
            padding: 12px 20px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            background-color: #2a2e3e;
            /* Dark gray button */
            color: #cfd4dc;
            transition: all 0.3s;
            border-radius: 0 6px 6px 0;
        }

        button:hover {
            background-color: #3a3f52;
        }

        .clear-btn {
            width: 100%;
            margin-top: 10px;
            border-radius: 6px;
            background-color: #3b3f50;
            /* Slightly different dark gray */
        }

        .clear-btn:hover {
            background-color: #4c5163;
        }

        h3 {
            margin-top: 20px;
            margin-bottom: 10px;
            color: #8ab4f8;
        }

        pre {
            background-color: #161b26;
            /* Terminal style */
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #2c313e;
            overflow-x: auto;
            font-size: 16px;
            color: #cfd4dc;
        }

        @media (max-width: 500px) {
            form {
                flex-direction: column;
            }

            input[type="text"],
            button {
                border-radius: 6px;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Remote Command Executor</h1>

        <form method="POST" action="{{ route('remote.execute') }}">
            @csrf
            <input type="text" name="command" placeholder="Enter command" required>
            <button type="submit">Execute</button>
        </form>

        <form method="POST" action="{{ route('remote.clear') }}">
            @csrf
            <button type="submit" class="clear-btn">Clear Output</button>
        </form>

        <h3>Output:</h3>
        <pre>{{ $output ?: 'No output yet' }}</pre>
    </div>
</body>

</html>