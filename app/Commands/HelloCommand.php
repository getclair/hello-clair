<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use LaravelZero\Framework\Components\Logo\FigletString;

class HelloCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'hello';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start the developer onboarding process';

    /**
     * @var string[]
     */
    protected $steps = [
        'install:cli-tools',
        'configure',
        'install:apps',
        'install:repos',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $title = (string) new FigletString('    '.config('logo.name'), config('logo'));
        $size = strlen($title);
        $spaces = str_repeat(' ', $size);

        $this->output->newLine();
        $this->output->writeln("<bg=#032A51;fg=white>$spaces$title$spaces</>");
        $this->output->newLine();

        $this->info('Hello! Welcome aboard, friend.');
        $this->info("\n");

        //        $firstName = $this->ask('First thing\'s first... what is your first name?');
        //        $email = $this->ask('And what is your Clair email address?');

        foreach ($this->steps as $step) {
            $this->call($step);
        }
    }
}
