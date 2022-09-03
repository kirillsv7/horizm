<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{

    public function __construct(
        protected User $user,
    ) {
    }

    public function all(): Collection
    {
        return $this->user::all();
    }

    public function getOrderedByAvgPostsRating(): Collection
    {
        return $this->user
            ->query()
            ->with('posts')
            ->withAvg('posts', 'rating')
            ->orderBy('posts_avg_rating', 'desc')
            ->get();
    }

    public function get(int $id): ?User
    {
        return $this->user
            ->query()
            ->find($id);
    }

    public function store(array $data): User
    {
        $model = $this->user
            ->newInstance($data);

        $model->save();

        return $model;
    }

    public function update(int $id, array $data): User
    {
        $model = $this->get($id);

        $model->fill($data)
              ->save();

        return $model;
    }
}