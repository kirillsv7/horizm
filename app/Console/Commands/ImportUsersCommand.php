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
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ImportUsersService $service)
    {
        if (!$this->confirm('Import users?', true)) {
            $this->warn('Users import cancelled!');

            return;
        }

        $service->handle();
        $this->info('Users imported!');
    }
}
