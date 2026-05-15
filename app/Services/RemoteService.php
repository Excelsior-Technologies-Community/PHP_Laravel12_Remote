<?php

namespace App\Services;

class RemoteService
{
    public function run(string $command): string
    {
        try {

            $output = [];
            $returnVar = 0;

            // Get Laravel project root
            $projectPath = base_path();

            // Execute command from project directory
            $fullCommand =
                "cd /d {$projectPath} && {$command} 2>&1";

            exec(
                $fullCommand,
                $output,
                $returnVar
            );

            if (!empty($output)) {
                return implode(
                    "\n",
                    $output
                );
            }

            return $returnVar === 0
                ? 'Command executed successfully, no output.'
                : 'Error executing command.';
        }
        catch (\Exception $e) {

            return "Error: "
                . $e->getMessage();
        }
    }
}