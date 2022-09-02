<?php

namespace App\Console\Commands;

use App\Services\ImportUsersService;
use Illuminate\Console\Command;

class ImportUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command import users, you must execute import:posts prevously';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        protected ImportUsersService $service
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
        if ($this->confirm('Import users?', true)) {
            $this->service->handle();
            $this->info('Users imported!');
        }else{
            $this->warn('Users import cancelled!');
        }
    }
}
