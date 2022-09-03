<?php

namespace App\Repositories;

use App\Contracts\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{

    public function __construct(
        protected Post $post,
    ) {
    }

    public function all(): Collection
    {
        return $this->post::all();
    }

    public function top(): Collection
    {
        return $this->post
            ->query()
            ->with('user:id,name')
            ->orderBy('rating', 'desc')
            ->groupBy('user_id')
            ->havingRaw('MAX(rating)')
            ->get();
    }

    public function get(int $id): ?Post
    {
        return $this->post
            ->query()
            ->find($id);
    }

    public function store(array $data): Post
    {
        $model = $this->post
            ->newInstance($data);

        $model->save();

        return $model;
    }

    public function update(int $id, array $data): Post
    {
        $model = $this->get($id);

        $model->fill($data)
              ->save();

        return $model;
    }
}