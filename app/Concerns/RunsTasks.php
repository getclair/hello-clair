<?php

namespace App\Concerns;

use Illuminate\Support\Facades\File;

trait RunsTasks
{
    protected function runTasks(array $tasks)
    {
        foreach ($tasks as $task) {
            $this->task($task['description'], function () use ($task) {
                if (array_key_exists('source', $task)) {
                    File::copy($task['source'], $task['destination']);
                }

                if (array_key_exists('command', $task)) {
                    $this->newLine();
                    $this->terminal()->output($this)->run($task['command']);
                }

                if (array_key_exists('method', $task) && method_exists($this, $task['method'])) {
                    $this->newLine();
                    call_user_func([$this, $task['method']]);
                }

                return true;
            });
        }
    }
}
