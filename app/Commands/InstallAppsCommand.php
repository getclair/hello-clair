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
    protected $signature = 'apps';

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

                // We only want to install an app if it passes checks based on it's "type"
                $checks = $config['type'] === 'anyOf' ? [$selection['check']] : Arr::pluck($selections, 'check');

                $this->task("Installing {$selection['name']}", function () use ($checks, $selection) {
                    if ($this->shouldInstallApp($checks)) {
                        $this->terminal()->output($this)->run($selection['command']);
                    }

                    return true;
                }, 'installing...');
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
        $choices = $this->buildQuestion($config);

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
     * Build and display apps question.
     *
     * @param $config
     * @param $options
     * @return array|string
     */
    protected function buildQuestion($config)
    {
        $term = $config['type'] === 'anyOf' ? 'apps' : 'app';

        return $this->choice(
            "Select the {$config['group']} {$term} you want to install",
            $this->buildOptions($config['options']),
            'none', null, $config['type'] === 'anyOf',
        );
    }

    /**
     * Build options for apps question.
     *
     * @param  array  $options
     * @return array
     */
    protected function buildOptions(array $options): array
    {
        $items = ['none' => 'None'];

        foreach ($options as $key => $option) {
            $items[$key] = "{$option['name']} ({$option['url']})";
        }

        return $items;
    }

    /**
     * Check if an app should be installed.
     *
     * @param  array  $checks
     * @return bool
     */
    protected function shouldInstallApp(array $checks): bool
    {
        $results = array_filter($checks, function ($check) {
            return $this->terminal()->run("mdfind \"kMDItemKind == 'Application'\" | grep -i $check")->ok();
        });

        return count($results) === 0;
    }
}
