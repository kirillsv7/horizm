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

        $userIds = $service->getUserIdsFromPosts();

        if (empty($userIds)) {
            $this->warn('No posts yet created!');

            return;
        }

        foreach ($userIds as $id) {
            $status = $service->handleUser($id);
            $this->info(sprintf('User %1$d %2$s', $id, $status));
        }

        $this->info('Users imported!');
    }
}
