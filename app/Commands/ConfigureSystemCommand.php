<?php

namespace App\Commands;

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
        $this->task('Setting up user settings...', function () {
            $this->addUserSettings();

            return true;
        }, '');

        $this->task('Setting up default config...', function () {
            $this->addConfigSettings();

            return true;
        }, '');

        $this->task('Setting up authentication...', function () {
            $this->setupAuthentication();

            return true;
        }, '');
    }

    /**
     * Add user values.
     */
    protected function addUserSettings()
    {
        $name = trim((string) $this->terminal()->run('git config --global user.name'));
        $email = trim((string) $this->terminal()->run('git config --global user.email'));
        $username = trim((string) $this->terminal()->run('git config --global user.username'));

        while (strlen($name) === 0) {
            $name = $this->ask('What is your name?', trim($name));
        }

        while (strlen($email) === 0) {
            $email = $this->ask('What is your Github user email?', trim($email));
        }

        while (strlen($username) === 0) {
            $username = $this->ask('What is your Github username?', trim($username));
        }

        $this->terminal()->output($this)->run("git config --global user.name '$name'");
        $this->terminal()->output($this)->run("git config --global user.email '$email'");
        $this->terminal()->output($this)->run("git config --global user.username '$username'");
        $this->terminal()->output($this)->run("git config --global github.user '$username'");
    }

    /**
     * Add .gitconfig settings.
     */
    protected function addConfigSettings()
    {
        $settings = [
            'filters' => [
                'clean' => 'git-lfs clean -- %f',
                'smudge' => 'git-lfs smudge -- %f',
                'process' => 'git-lfs filter-process',
                'required' => 'true',
            ],
            'pull' => [
                'rebase' => 'false',
            ],
        ];

        foreach ($settings as $group => $values) {
            foreach ($values as $key => $value) {
                $this->terminal()->output($this)->run("git config --global $group.$key '$value'");
            }
        }
    }

    /**
     * Set up Github authentication using GCM.
     */
    protected function setupAuthentication()
    {
        if ($this->shouldInstall('which git-credential-manager-core')) {
            if ($this->confirm('Do you want to set up Github authentication now?', true)) {
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
        $path = home_path('/.gitignore');

        if (! is_file($path)) {
            $url = 'https://raw.githubusercontent.com/freekmurze/dotfiles/master/shell/.global-gitignore';
            file_put_contents($path, file_get_contents($url));
        }

        $this->terminal()->output($this)->run("git config --global core.excludesfile $path");
    }
}
