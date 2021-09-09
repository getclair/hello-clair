<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use ZipArchive;

class SetupOhMyZsh extends StepCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'configure:oh-my-zsh';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Configure Oh My Zsh.';

    protected static $packageName = 'iterm2env';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->newLine();
        $this->line('Configuring Oh My Zsh...');
        $this->newLine();

        $tasks = [
            [
                'description' => 'Adding Zsh theme...',
                'source' => resource_path('config/cobalt2-custom.zsh-theme'),
                'destination' => $this->homeDirectory('.oh-my-zsh/themes/cobalt2-clair.zsh-theme'),
            ],
            [
                'description' => 'Adding iTerm2 theme...',
                'source' => resource_path('config/iTerm2-custom.zsh'),
                'destination' => $this->homeDirectory('.oh-my-zsh/custom/iTerm2-clair.zsh'),
            ],
            [
                'description' => 'Adding .zshrc...',
                'source' => resource_path('config/.zshrc'),
                'destination' => $this->homeDirectory('/.zshrc-tmp'),
            ],
            [
                'description' => 'Configuring shell aliases...',
                'command' => "echo 'alias python2=\"/usr/bin/python\"' >> ~/.zshrc && echo 'alias python=\"/usr/local/bin/python3\"' >> ~/.zshrc && source ~/.zshrc",
            ],
            [
                'description' => 'Installing Powerline...',
                'command' => 'pip3 install iterm2 && pip3 install --user powerline-status && cd ~ && git clone https://github.com/powerline/fonts && cd fonts && ./install.sh && cd ~',
            ],
            [
                'description' => 'Configuring iTerm2...',
                'source' => resource_path('scripts/default-profile.py'),
                'destination' => $this->homeDirectory('/Library/Application Support/iTerm2/Scripts/AutoLaunch/clair-profile.py'),
                'method' => 'installiTerm2Python',
            ],
        ];

        foreach ($tasks as $task) {
            $this->task($task['description'], function () use ($task) {
                if (array_key_exists('source', $task)) {
                    File::copy($task['source'], $task['destination']);
                }

                if (array_key_exists('command', $task)) {
                    $this->newLine();
                    $this->terminal()->output($this)->run($task['command']);
                }

                if (array_key_exists('method', $task) && method_exists($this, $task['method'])) {
                    $this->newLine();
                    call_user_func([$this, $task['method']]);
                }

                return true;
            }, '');
        }

        $this->newLine();
        $this->comment('Oh My Zsh successfully configured.');
        $this->newLine();
    }

    protected function installiTerm2Python()
    {
        // Get manifest...
        $this->comment('Downloading package...');

        $manifest = json_decode(file_get_contents('https://iterm2.com/downloads/pyenv/manifest.json'), true);

        // Download file locally.
        $url = $manifest[0]['url'];

        $tempFolder = storage_path('tmp');
        $path = $tempFolder.'/env.zip';

        if (File::isDirectory($tempFolder)) {
            File::deleteDirectory($tempFolder);
        }

        File::makeDirectory($tempFolder);

        $remote_file_contents = file_get_contents($url);

        file_put_contents($path, $remote_file_contents);

        // Unzip
        $this->comment('Unzipping package...');

        $zipArchive = new ZipArchive();

        if ($zipArchive->open($path)) {
            $zipArchive->extractTo($tempFolder);
            $zipArchive->close();

            // Delete the zip file.
            File::delete($path);

            $sourceFolder = $tempFolder.'/'.static::$packageName;

            // Move files.
            $versions = $manifest[0]['python_versions'];
            $versions[] = '';

            foreach ($versions as $version) {
                $folder = static::$packageName;

                if (strlen($version) > 0) {
                    $folder .= '-'.$version;
                }

                $destinationFolder = $this->homeDirectory('Library/ApplicationSupport/iTerm2/'.$folder);

                $this->comment("Moving package to: $destinationFolder");

                $this->terminal()->output($this)->with([
                    'source' => $sourceFolder.'/',
                    'destination' => $destinationFolder,
                ])->run('rsync --progress --stats --human-readable --recursive --timeout=300 {{ $source }} {{ $destination }}');
            }

            // Cleanup
            $this->comment('Cleaning up...');
            File::deleteDirectory($tempFolder);
        }
    }
}
