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