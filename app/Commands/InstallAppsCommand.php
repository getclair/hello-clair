<?php

namespace App\Commands;

use Illuminate\Support\Arr;

class InstallAppsCommand extends StepCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install:apps';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install selected apps.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line("\nReady to install applications...\n");

        foreach (config('manifest.apps') as $group => $config) {
            $selections = $this->getSelections($config);

            if (count($selections) === 0) {
                continue;
            }

            foreach ($selections as $selection) {

                // We only want to install an app if it passes checks based on its "type"
                $checks = $config['type'] === 'anyOf' ? [$selection['check']] : Arr::pluck($selections, 'check');

                if ($this->shouldInstall($checks)) {
                    $this->install($selection);
                }
            }
        }
    }

    /**
     * Ask for app selection(s) and return the resolved configs.
     *
     * @param $config
     * @return array
     */
    protected function getSelections($config): array
    {
        $term = $config['type'] === 'anyOf' ? 'apps' : 'app';

        $choices = $this->buildQuestion(
            "Select the {$config['group']} {$term} you want to install",
            $this->buildOptions($config['options']),
            $config['type'] === 'anyOf'
        );

        if (! is_array($choices)) {
            $choices = [$choices];
        }

        if (in_array('none', $choices)) {
            return [];
        }

        return array_map(function ($choice) use ($config) {
            return $config['options'][$choice];
        }, $choices);
    }

    /**
     * Check if an app should be installed.
     *
     * @param $checks
     * @return bool
     */
    protected function shouldInstall($checks): bool
    {
        $results = array_filter($checks, function ($check) {
            return $this->terminal()->run("mdfind \"kMDItemKind == 'Application'\" | grep -i $check")->ok();
        });

        return count($results) === 0;
    }
}
