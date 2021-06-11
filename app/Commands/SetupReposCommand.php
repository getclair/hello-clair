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
        $this->repoFolder = $this->ask('What is your repo folder?', $this->homeDirectory('Web'));

        $selections = $this->getSelections(config('manifest.repos'));

        if (count($selections) > 0) {
            foreach ($selections as $key => $selection) {
                $this->task("Installing {$selection['name']}", function () use ($key, $selection) {
                    if ($this->shouldSetupRepo($key)) {
                        $method = Str::camel("setup_{$selection['type']}");

                        if (method_exists($this, $method)) {
                            $this->$method($key, $selection);
                        }
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
        $choices = $this->buildQuestions($config);

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
    protected function buildQuestions()
    {
        return $this->choice(
            'Select the repos you want to clone and set up (comma-separated)',
            $this->buildOptions(),
            'none', null, true
        );
    }

    /**
     * Return repo options.
     *
     * @return array
     */
    protected function buildOptions(): array
    {
        return array_merge(
            ['none' => 'None'],
            collect(config('manifest.repos'))->mapWithKeys(function ($item, $key) {
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
        $this->terminal($path)->output($this)->run('php -r "file_exists(\'.env\') || copy(\'.env.example\', \'.env\');"');
        $this->terminal($path)->output($this)->run('php artisan key:generate --ansi');

        $this->terminal($path)->output($this)->run("valet link {$name}");

        Env::set($path.'/.env', 'APP_NAME', $config['name']);
        Env::set($path.'/.env', 'APP_URL', "http://{$name}.test");

        $this->terminal($path)->output($this)->run('composer dump');
    }
}
