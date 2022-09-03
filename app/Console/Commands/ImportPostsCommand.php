<?php

namespace App\Console\Commands;

use App\Services\ImportPostsService;
use Illuminate\Console\Command;

class ImportPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command import posts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ImportPostsService $service)
    {
        if (!$this->confirm('Import posts?', true)) {
            $this->warn('Posts import cancelled!');

            return;
        }

        $service->handle();
        $this->info('Posts imported!');
    }
}
