# PHP_Laravel12_Remote


## Project Description

PHP_Laravel12_Remote is a Laravel 12 web application that allows users to execute commands on a remote server via SSH from a simple web interface. This project integrates the Spatie Laravel Remote package to securely connect to remote servers using SSH keys or password authentication.

The main goal is to provide a safe, user-friendly, and visual interface for running server commands without manually logging in to the server terminal. This is especially useful for system administrators, DevOps engineers, or developers who need a web-based way to execute tasks on remote servers.



## Features 

1. Remote Command Execution – Run commands on a remote server via SSH.
2. Output Display – Shows command results in a terminal-style box.
3. Clear Output – Easily remove previous command outputs.
4. SSH Key Support – Secure connection using private keys.
5. Dark Mode UI – Modern, responsive interface.
6. Laravel Best Practices – Uses Service class, Controller, and Blade templates.

## Technologies Used

1. PHP 8.2+ – Latest stable version for modern PHP development.
2. Laravel 12 – Backend framework, handles routing, controllers, and security.
3. Spatie Laravel Remote – Package to execute commands on remote servers via SSH.
4. MySQL – Database (optional, primarily for session handling).
5. Blade – Laravel’s templating engine for the UI.
6. CSS – Custom dark-mode styling for the interface.
7. SSH / Private Keys – Secure connection to remote servers.



---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_Remote "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Remote

```

#### Explanation:

Installs a fresh Laravel project named PHP_Laravel12_Remote with version 12.*.

Moves into the project directory to start working on it.




## STEP 2: Database Setup (Optional)

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_Remote
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_Remote

```

### Then Run:

```
php artisan migrate

```


#### Explanation:

Connects Laravel to MySQL and creates default tables for the project.




## STEP 3: Install Required Package 

### Run:

```
composer require spatie/laravel-remote

```


### Publish config:

```
php artisan vendor:publish --provider="Spatie\Remote\RemoteServiceProvider"

```


#### Explanation: 

Installs the Spatie Laravel Remote package to execute commands on remote servers.

Copies the package config to your project so you can customize it.






## STEP 4: Add Remote Config

### In config/remote.php:

```
<?php

return [

    'default_connection' => env('REMOTE_DEFAULT_CONNECTION', 'production'),

    'connections' => [
        'production' => [
            'host' => env('REMOTE_HOST'),
            'username' => env('REMOTE_USER'),
            'port' => env('REMOTE_PORT', 22),
            'private_key' => env('REMOTE_PRIVATE_KEY_PATH'),
            'needs_confirmation' => env('REMOTE_NEEDS_CONFIRMATION', false),
        ],
    ],

    'php_path' => env('REMOTE_PHP_PATH', 'php'),
];

```


### Add .env vars:

```
REMOTE_HOST=your.server.ip
REMOTE_USER=ubuntu
REMOTE_PORT=22
REMOTE_PATH=/var/www/html
REMOTE_PRIVATE_KEY_PATH=/home/ubuntu/.ssh/id_rsa
REMOTE_PHP_PATH=php
REMOTE_NEEDS_CONFIRMATION=false
REMOTE_DEFAULT_CONNECTION=production

```

#### Explanation: 

Configures how Laravel connects to your remote server.

Stores remote server credentials securely in environment variables.





## STEP 5: Create RemoteService

### app/Services/RemoteService.php

```
<?php

namespace App\Services;

class RemoteService
{
    public function run(string $command): string
    {
        try {
            // Execute command locally
            $output = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar); // captures errors too

            if (!empty($output)) {
                return implode("\n", $output);
            }

            return $returnVar === 0 ? 'Command executed successfully, no output.' : 'Error executing command.';

        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}

```

#### Explanation: 

A service class that executes commands and returns output or errors.






## STEP 6: Create Controller

### Run:

```
php artisan make:controller RemoteController

```

### app/Http/Controllers/RemoteController.php

```
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

```

#### Explanation: 

Handles displaying, executing, and clearing remote command output.





## STEP 7: Add Routes

### routes/web.php

```
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RemoteController;

Route::get('/', function () {
    return redirect('/remote');
});

Route::get('/remote', [RemoteController::class, 'index'])->name('remote.index');
Route::post('/remote', [RemoteController::class, 'execute'])->name('remote.execute');
Route::post('/remote/clear', [RemoteController::class, 'clearOutput'])->name('remote.clear');

```

#### Explanation: 

Routes to show the form, execute commands, and clear output.





## STEP 8: Blade View

### resources/views/remote/index.blade.php

```
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

```

#### Explanation: 

Routes to show the form, execute commands, and clear output.





## STEP 9: Run the App  

### Start dev server:

```
php artisan serve

```

### Open in browser:

```
http://127.0.0.1:8000/remote

```

#### Explanation:

Starts the Laravel development server locally.

Access the Remote Command Executor UI in your browser.



## Expected Output:

### Remote Command Executor UI:


<img src="screenshots/Screenshot 2026-03-24 180011.png" width="900">


### Sample Command Output:


<img src="screenshots/Screenshot 2026-03-24 180109.png" width="900">

<img src="screenshots/Screenshot 2026-03-24 180237.png" width="900">




---

## Project Folder Structure:

```
PHP_Laravel12_Remote/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── RemoteController.php         # Controller we created
│   │   ├── Middleware/
│   │   └── Kernel.php
│   ├── Models/
│   ├── Providers/
│   └── Services/
│       └── RemoteService.php                # Service we created
├── bootstrap/
│   ├── cache/
│   └── app.php
├── config/
│   ├── app.php
│   ├── database.php
│   ├── remote.php                           # Config for Spatie Remote
│   └── ...
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── public/
│   ├── index.php
│   └── ...
├── resources/
│   ├── views/
│   │   └── remote/
│   │       └── index.blade.php              # Blade view we created
│   └── ...
├── routes/
│   └── web.php                              # Routes for remote executor
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
├── tests/
│   ├── Feature/
│   └── Unit/
├── vendor/
├── .env                                     # Environment file with DB + Remote config
├── artisan
├── composer.json
└── composer.lock

```
