<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

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
        $this->info('Hello! Welcome aboard, friend.');

//        $firstName = $this->ask('First thing\'s first... what is your first name?');
//        $email = $this->ask('And what is your Clair email address?');

        foreach ($this->steps as $step) {
            $this->call($step);
        }
    }
}
