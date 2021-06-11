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
    protected $signature = 'cli-tools';

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
            $this->task("Installing {$selection['name']}", function () use ($selection) {
                if ($this->shouldInstallCliTool($selection['check'])) {
                    $this->terminal()->output($this)->run($selection['command']);
                }

                return true;
            }, 'installing...');
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
        $choices = $this->buildQuestion();

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
     * Build and display apps question.
     *
     * @return array|string
     */
    protected function buildQuestion()
    {
        return $this->choice(
            'Select the environments you want to install',
            [
                'none' => 'None',
                'backend' => 'Backend',
                'frontend' => 'Frontend',
            ],
            'none', null, true
        );
    }

    /**
     * Check if a CLI tool should be installed, or if it exists already.
     *
     * @param $check
     * @return bool
     */
    protected function shouldInstallCliTool($check): bool
    {
        $response = $this->terminal()->run($check);

        return ! $response->ok()
            || $response->getExitCode() === 1
            || Str::contains((string) $response, self::$errorChecks);
    }
}
