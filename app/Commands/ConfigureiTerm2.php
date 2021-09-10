<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use ZipArchive;

class ConfigureiTerm2 extends StepCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'configure:iterm2';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install the Python API for iTerm2.';

    protected static $packageName = 'iterm2env';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Get manifest...
        $this->comment('Downloading python for iTerm2...');

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

                $destinationFolder = $this->homePath('Library/ApplicationSupport/iTerm2/'.$folder);

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

        $this->line('python for iTerm2 installed.');
    }
}
