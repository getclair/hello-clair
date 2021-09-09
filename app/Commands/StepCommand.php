<?php

namespace App\Commands;

use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use TitasGailius\Terminal\Terminal;

abstract class StepCommand extends Command
{
    /**
     * @param  null  $context
     * @return mixed
     */
    protected function terminal($context = null)
    {
        return Terminal::in($context ?? $this->homeDirectory());
    }

    /**
     * @param  null  $path
     * @return string
     */
    protected function homeDirectory($path = null): string
    {
        if (Str::startsWith($path, '/')) {
            $path = Str::replaceFirst('/', '', $path);
        }

        return implode('/', [getenv('HOME'), $path]);
    }
}
