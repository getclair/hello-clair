<?php

namespace App\Commands;

use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitRepository;
use Eco\Env;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SetupReposCommand extends StepCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install:repos
                            {--f|force : Force a fresh install}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Clone and set up selected repos.';

    /**
     * Local repo folder.
     *
     * @var
     */
    protected $repoFolder;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->repoFolder = $this->ask('What is your repo folder?', $this->homePath('Web'));

        File::ensureDirectoryExists($this->repoFolder);

        $selections = $this->getSelections(config('manifest.repos'));

        if (count($selections) > 0) {
            foreach ($selections as $folder => $selection) {
                $this->task("Installing {$selection['name']}", function () use ($folder, $selection) {
                    if ($this->shouldSetupRepo($folder)) {
                        $method = Str::camel("setup_{$selection['type']}");

                        if (method_exists($this, $method)) {
                            $this->$method($folder, $selection);
                        }
                    }

                    return true;
                }, 'installing...');
            }

            $this->info(count($selections).' '.Str::plural('project', count($selections)).' successfully installed!');

            $this->table(
                ['Project', 'Path', 'URL', 'Type'],
                collect($selections)->map(function ($item, $key) {
                    return [
                        $item['name'],
                        $this->repoFolder($key),
                        in_array($item['type'], ['laravel']) ? "http://{$key}.test" : '--',
                        Str::title(str_replace('-', ' ', $item['type'])),
                    ];
                })->toArray(),
            );
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
        $choices = $this->buildQuestions(config('manifest.repos'));

        if (! is_array($choices)) {
            $choices = [$choices];
        }

        if (in_array('none', $choices)) {
            return [];
        }

        $selections = [];

        foreach ($choices as $choice) {
            $selections[$choice] = $config[$choice];
        }

        return $selections;
    }

    /**
     * Return questions.
     *
     * @return array|string
     */
    protected function buildQuestions(array $choices)
    {
        return $this->choice(
            'Select the repos you want to clone and set up (comma-separated)',
            $this->buildOptions($choices),
            'none', null, true
        );
    }

    /**
     * Return repo options.
     *
     * @param  array  $options
     * @return array
     */
    protected function buildOptions(array $options = []): array
    {
        return array_merge(
            ['none' => 'None'],
            collect($options)->mapWithKeys(function ($item, $key) {
                return [$key => $item['name']];
            })->toArray()
        );
    }

    /**
     * Check if the repo folder already exists.
     *
     * @param $folder
     * @return bool
     */
    protected function shouldSetupRepo($folder): bool
    {
        return ! File::exists($this->repoFolder($folder)) || $this->option('force');
    }

    /**
     * Return built repo folder.
     *
     * @param $folder
     * @return string
     */
    protected function repoFolder($folder): string
    {
        return $this->repoFolder.'/'.$folder;
    }

    /**
     * Set up Laravel-based project.
     *
     * @param $folder
     * @param $config
     * @throws \CzProject\GitPhp\GitException
     */
    protected function setupLaravel($folder, $config)
    {
        $path = $this->repoFolder($folder);

        if ($this->option('force') && File::exists($path)) {
            File::deleteDirectory($path);
        }

        $repo = $this->cloneRepo($config['ssh'], $path);

        $this->runComposerInstall($path);
        $this->configureLaravelApp($path, $folder, $config);
        $this->runNpmInstall($path);
    }

    /**
     * Set up React Native-based project.
     *
     * @param $folder
     * @param $config
     */
    protected function setupReactNative($folder, $config)
    {
        $path = $this->repoFolder($folder);

        if ($this->option('force') && File::exists($path)) {
            File::deleteDirectory($path);
        }

        $repo = $this->cloneRepo($config['ssh'], $path);

        $this->runYarnInstall($path);
    }

    /**
     * Return the new git repository.
     *
     * @param $ssh
     * @param $path
     * @return GitRepository
     * @throws \CzProject\GitPhp\GitException
     */
    protected function cloneRepo($ssh, $path): GitRepository
    {
        $git = new Git();

        return $git->cloneRepository($ssh, $path);
    }

    /**
     * Run "composer install" within the path context.
     *
     * @param $path
     */
    protected function runComposerInstall($path)
    {
        $this->terminal($path)->output($this)->run('composer install');
    }

    /**
     * Run "npm install" within the path context.
     *
     * @param $path
     */
    protected function runNpmInstall($path)
    {
        $this->terminal($path)->output($this)->run('npm install');
    }

    /**
     * Run "yarn install" within the path context.
     *
     * @param $path
     */
    protected function runYarnInstall($path)
    {
        $this->terminal($path)->output($this)->run('yarn install');
    }

    /**
     * @param $path
     * @param $name
     * @param  array  $config
     */
    protected function configureLaravelApp($path, $name, array $config)
    {
        // Configure .env keys
        $this->terminal($path)->output($this)->run('php -r "file_exists(\'.env\') || copy(\'.env.example\', \'.env\');"');
        $this->terminal($path)->output($this)->run('php artisan key:generate --ansi');

        // Set .env variables
        foreach ($this->envVariables($config, $name) as $key => $value) {
            Env::set($path.'/.env', $key, $value);
        }

        // Setup database
        if ($this->shouldSetupDatabase()) {
            $this->terminal($path)->output($this)->run("createdb $name");
            $this->terminal($path)->output($this)->run('php artisan migrate');
        }

        // Clear app
        $this->terminal($path)->output($this)->run('composer dump');

        // Create local URL
        $this->terminal($path)->output($this)->run("valet link {$name}");
    }

    /**
     * @return bool
     */
    protected function shouldSetupDatabase(): bool
    {
        return $this->terminal()->run('which createdb')->ok();
    }

    /**
     * @param  array  $config
     * @param $name
     * @return array
     */
    protected function envVariables(array $config, $name): array
    {
        return [
            'APP_NAME' => $config['name'],
            'APP_URL' => "http://{$name}.test",
            'DB_CONNECTION' => 'pgsql',
            'DB_HOST' => '127.0.0.1',
            'DB_PORT' => '5432',
            'DB_DATABASE' => $name,
            'DB_USERNAME' => 'postgres',
            'DB_PASSWORD' => '',
        ];
    }
}
