<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;

class ConfigureSystemCommand extends StepCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'configure';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Configure 3rd party auth and other system settings.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->setupGlobalIgnore();
        $this->configureGithub();
    }

    /**
     * Configure Github profile.
     */
    protected function configureGithub()
    {
        $current_username = $this->terminal()->run('git config user.name');
        $current_email = $this->terminal()->run('git config user.email');

        $git_username = $this->ask('What is your Git name', trim((string) $current_username));
        $git_email = $this->ask('What is your Git email', trim((string) $current_email));

        $this->terminal()->output($this)->run("git config --global user.name '$git_username'");
        $this->terminal()->output($this)->run("git config --global user.email '$git_email'");

        if ($this->confirm('Do you want to set up Github authentication now?', true)) {
            if ($this->shouldInstall('which git-credential-manager-core')) {
                $this->task('Installing GCM...', function () {
                    if ($this->shouldInstall('which git')) {
                        $this->terminal()->output($this)->run('brew install git');
                    }

                    $this->terminal()->output($this)->run('brew tap microsoft/git');
                    $this->terminal()->output($this)->run('brew install --cask git-credential-manager-core');

                    $this->comment('Git Credential Manager was successfully installed. You will be prompted to log in via the browser on your first connection to git.');
                });
            }
        }
    }

    /**
     * Setup global .gitignore file to always ignore junk files.
     */
    protected function setupGlobalIgnore()
    {
        $path = $_SERVER['HOME'].'/.gitignore';

        if (! is_file($path)) {
            $url = 'https://raw.githubusercontent.com/freekmurze/dotfiles/master/shell/.global-gitignore';
            file_put_contents($path, file_get_contents($url));
        }

        $this->terminal()->output($this)->run("git config --global core.excludesfile $path");
    }
}
