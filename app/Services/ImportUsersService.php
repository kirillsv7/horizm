<?php

namespace App\Services;

use App\Contracts\PostRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Http;

class ImportUsersService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected PostRepositoryInterface $postRepository,
        protected Http $client,
    ) {
    }

    public function getUserFromApi($id): array
    {
        return $this->client::get(env('API_USERS_URL'), [
            'id' => $id,
        ])->json()[0];
    }

    public function getUserIdsFromPosts(): array
    {
        return $this->postRepository->all()->pluck('user_id')->unique()->values()->toArray();
    }

    public function handleUser(int $id): string
    {
        $existentUser = $this->userRepository->get($id);

        if ($existentUser) {
            return 'already exists';
        }

        $user     = $this->getUserFromApi($id);
        $userData = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'city'  => $user['address']['city'],
        ];
        $this->userRepository->store($userData);

        return 'created';
    }
}