<?php

namespace App\Services;

use App\Contracts\PostRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Output\ConsoleOutput;

class ImportUsersService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected PostRepositoryInterface $postRepository,
        protected Http $client,
        protected ConsoleOutput $consoleOutput,
    ) {
    }

    public function handle()
    {
        $userIds = $this->postRepository->all()->pluck('user_id')->unique()->values();

        foreach ($userIds as $id) {
            $existentUser = $this->userRepository->get($id);

            if ($existentUser) {
                $status = 'already exists';
            } else {
                $user     = $this->getUser($id);
                $userData = [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'city'  => $user['address']['city'],
                ];
                $this->userRepository->store($userData);
                $status = 'created';
            }

            if (app()->runningInConsole()) {
                $this->consoleOutput->write(sprintf('User %1$d %2$s', $id, $status), true);
            }
        }
    }

    public function getUser($id): array
    {
        return $this->client::get(env('API_USERS_URL'), [
            'id' => $id,
        ])->json()[0];
    }


}