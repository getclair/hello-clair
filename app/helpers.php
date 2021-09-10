<?php

use Illuminate\Support\Str;

if (! function_exists('home_path')) {
    function home_path($path = null): string
    {
        if (Str::startsWith($path, '/')) {
            $path = Str::replaceFirst('/', '', $path);
        }

        return implode('/', [getenv('HOME'), $path]);
    }
}
