<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RemoteServer;

class RemoteServerSeeder extends Seeder
{
    public function run()
    {
        // Clear existing servers
        RemoteServer::truncate();

        $servers = [
            [
                'name' => 'Local Server',
                'host' => '127.0.0.1',
                'port' => 22,
                'username' => 'root',
                'password' => null,
                'private_key' => null,
                'auth_type' => 'password',
                'is_active' => true,
                'is_default' => true
            ],
            [
                'name' => 'Production Server',
                'host' => '192.168.1.100',
                'port' => 22,
                'username' => 'admin',
                'password' => 'your_password_here',
                'private_key' => null,
                'auth_type' => 'password',
                'is_active' => true,
                'is_default' => false
            ],
            [
                'name' => 'Staging Server',
                'host' => '192.168.1.101',
                'port' => 22,
                'username' => 'deploy',
                'password' => 'your_password_here',
                'private_key' => null,
                'auth_type' => 'password',
                'is_active' => true,
                'is_default' => false
            ]
        ];

        foreach ($servers as $server) {
            RemoteServer::create($server);
        }

        $this->command->info('✅ Remote servers seeded successfully!');
        $this->command->info('Default server: ' . RemoteServer::getDefault()->name);
    }
}