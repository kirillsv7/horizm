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

            $message = "User $id ";

            if ($existentUser) {
                $message .= 'already exists';
            } else {
                $user     = $this->getUser($id);
                $userData = [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'city'  => $user['address']['city'],
                ];
                $this->userRepository->store($userData);
                $message .= 'created';
            }

            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                $this->consoleOutput->write($message, true);
            }
        }
    }

    public function getUser($id): array
    {
        return $this->client::get('https://jsonplaceholder.typicode.com/users', [
            'id' => $id,
        ])->json()[0];
    }


}