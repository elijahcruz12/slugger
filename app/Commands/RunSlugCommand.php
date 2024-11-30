<?php

namespace App\Commands;

use App\Services\Slugger;
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

        // There may be multiple dots in the filename, so we need to slugify the filename
        $slugger = new Slugger($file);

        if(!$this->option('dry')) {
            $slug = $slugger->getSlug();
            $new_filename = Str::finish(pathinfo($file, PATHINFO_DIRNAME), '/') . $slug;
            if($slugger->wouldOverwrite()) {
                rename($file, $new_filename);
                $this->info('Renamed ' . $file . ' to ' . $slug);
            }
        }
        else {
            if($slugger->wouldOverwrite()) {
                $this->warn('Would overwrite ' . $file . ' with ' . $slugger->getSlug());
                return;
            }
        }
    }
}
