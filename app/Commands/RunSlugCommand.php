<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
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
            $files = array_diff(scandir(getcwd()), ['.', '..']);
            foreach($files as $file) {
                $this->runSlug($file);
            }
        } else if($this->option('file')) {
            $this->runSlug($this->option('file'));
        } else {
            $this->error('No file specified');
        }
    }

    private function runSlug(?string $file = null)
    {
        $this->info($file ?? 'No file specified');

    }
}
