<?php

namespace App\Concerns;

trait HandlesInstallation
{
    use RunsTasks;

    /**
     * Install the choice using the provided configuration.
     *
     * @param  array  $config
     * @param  string  $message
     */
    public function install(array $config, string $message = 'installing...')
    {
        $description = array_key_exists('description', $config) ? $config['description'] : "Installing {$config['name']}...";

        $this->task($description, function () use ($config) {
            $this->terminal()->output($this)->run($config['command']);

            if (array_key_exists('tasks', $config)) {
                $this->runTasks($config['tasks']);
            }
        }, $message);
    }
}
