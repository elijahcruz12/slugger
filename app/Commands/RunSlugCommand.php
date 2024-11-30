<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class RunSlugCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slug:run {--all : Run on all FILES in the current directory} {--file= : Run on a specific FILE in the current directory} {--dry : Dry run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the slug command on a file or all files in the current directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if($this->option('all')) {
            // Get only files in the current directory
            $files = array_filter(scandir(getcwd()), function($file) {
                return is_file($file);
            });
            foreach($files as $file) {
                $this->runSlug($file);
            }
        } else if($this->option('file')) {
            if(!is_file($this->option('file'))) {
                $this->error('File does not exist');
                return;
            }
            $this->runSlug($this->option('file'));
        } else {
            $this->error('No file specified');
        }
    }

    private function runSlug(string $file = null)
    {
        // Make sure file is not a directory
        if(is_dir($file)) {
            return;
        }

        // Get just the filename (no path, no extension)
        $filename = pathinfo($file, PATHINFO_FILENAME);

        // Get the file extension
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        // There may be multiple dots in the filename, so we need to slugify the filename
        $slug = Str::slug($filename,
            dictionary: [
                '@' => 'at',
                '.' => '.',
                '_' => '-',
                ' ' => '-',
            ]
        );

        // If the file has an extension, append it to the slug
        if($extension) {
            $slug .= '.' . $extension;
        }

        $this->info($slug);

        if(!$this->option('dry')) {
            // Rename the file
            rename($file, $slug);
            $this->info('Renamed ' . $file . ' to ' . $slug);
        }
        else {
            $this->info('Dry run, not renaming ' . $file . ' to ' . $slug);
        }
    }
}
