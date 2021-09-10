<?php

namespace App\Commands;

use App\Concerns\HandlesChoices;
use App\Concerns\HandlesInstallation;
use LaravelZero\Framework\Commands\Command;
use TitasGailius\Terminal\Terminal;

abstract class StepCommand extends Command
{
    use HandlesChoices;
    use HandlesInstallation;

    /**
     * @param  null  $context
     * @return mixed
     */
    protected function terminal($context = null)
    {
        return Terminal::in($context ?? $this->homePath());
    }

    /**
     * @param  null  $path
     * @return string
     */
    protected function homePath($path = null): string
    {
        return home_path($path);
    }

    /**
     * Checks if the option should be installed based on the "checks" value.
     *
     * @param $checks
     * @return bool
     */
    protected function shouldInstall($checks): bool
    {
        if (! is_array($checks)) {
            $checks = [$checks];
        }

        foreach ($checks as $check) {
            if ($this->terminal()->run($check)->ok()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a piece of software is installed.
     *
     * @param  string  $app
     * @return bool
     */
    protected function appCheck(string $app): bool
    {
        return $this->terminal()->run("mdfind \"kMDItemKind == 'Application'\" | grep -i $app")->ok();
    }
}
