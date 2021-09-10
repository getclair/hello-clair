<?php

namespace App\Commands;

use Illuminate\Support\Str;

class InstallCliToolsCommand extends StepCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install:cli-tools';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Setup CLI tools.';

    /**
     * @var string[]
     */
    protected static $errorChecks = [
        'No such keg',
        'xcode-select: error',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line("OK, let's install some CLI tools...\n");

        $groups = config('manifest.cli');

        $selections = array_values(array_merge(
            $groups['global'],
            $this->getSelections($groups)
        ));

        foreach ($selections as $selection) {
            if ($this->shouldInstall($selection['check'])) {
                $this->install($selection);
            }
        }
    }

    /**
     * Ask for app selection(s) and return the resolved configs.
     *
     * @param  array  $groups
     * @return array
     */
    protected function getSelections(array $groups): array
    {
        $choices = $this->buildQuestion(
            'Select the environments you want to install',
            [
                'backend' => 'Backend',
                'frontend' => 'Frontend',
            ],
            true
        );

        if (! is_array($choices)) {
            $choices = [$choices];
        }

        if (in_array('none', $choices)) {
            return [];
        }

        $selections = [];

        foreach ($choices as $choice) {
            foreach ($groups[$choice] as $tool) {
                $selections[] = $tool;
            }
        }

        return $selections;
    }

    /**
     * Check if a CLI tool should be installed, or if it exists already.
     *
     * @param $checks
     * @return bool
     */
    protected function shouldInstall($checks): bool
    {
        if (! array_key_exists('check', $checks)) {
            return true;
        }

        $response = $this->terminal()->run($checks['check']);

        return ! $response->ok()
            || $response->getExitCode() === 1
            || Str::contains((string) $response, self::$errorChecks);
    }
}
