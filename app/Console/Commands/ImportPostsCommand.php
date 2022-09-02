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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        protected ImportPostsService $service
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->confirm('Import posts?', true)) {
            $this->service->handle();
            $this->info('Posts imported!');
        }else{
            $this->warn('Posts import cancelled!');
        }
    }
}
