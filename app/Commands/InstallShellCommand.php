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
        $this->line("Installing shell...\n");

        $options = config('manifest.shell.options');

        $choice = $this->buildQuestion($options);

        if ($choice !== 'none') {
            $config = $options[$choice];

            $this->installShell($config);
        }
    }

    /**
     * Ask for app selection(s) and return the resolved configs.
     *
     * @param  array  $options
     * @return string
     */
    protected function buildQuestion(array $options): string
    {
        $choices = ['none' => 'None'];

        foreach ($options as $key => $option) {
            $choices[$key] = $option['name'];
        }

        return $this->choice(
            'Select the Unix shell you want to install',
            $choices,
            'none', null, false,
        );
    }

    /**
     * Install the chosen shell.
     *
     * @param  array  $config
     */
    public function installShell(array $config)
    {
        if (! $this->terminal()->run($config['check'])->ok()) {
            $this->terminal()->output($this)->run($config['command']);
        }
    }
}
