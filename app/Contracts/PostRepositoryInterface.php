<?php

namespace App\Contracts;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface
{

    public function all(): Collection;

    public function get(int $id): ?Post;

    public function store(array $data): Post;

    public function update(int $id, array $data): Post;

    public function top(): Collection;
}