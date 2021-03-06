<?php

namespace App\Commands;

class InstallShellCommand extends StepCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install:shell';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Setup shell.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->appCheck('iterm2')) {
            $this->error('You must have iTerm2 installed before adding a shell.');
        } else {
            $this->line("Installing shell...\n");

            $options = config('manifest.shell.options');

            $choice = $this->buildQuestion('Select the Unix shell you want to install', $options);

            if ($choice !== 'none') {
                $config = $options[$choice];

                if ($this->shouldInstall($config['check'])) {
                    $this->install($config);
                }
            }
        }
    }
}
