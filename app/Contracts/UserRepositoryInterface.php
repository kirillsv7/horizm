<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{

    public function all(): Collection;

    public function getByAvgPostsRating(): Collection;

    public function get(int $id): ?User;

    public function store(array $data): User;
}