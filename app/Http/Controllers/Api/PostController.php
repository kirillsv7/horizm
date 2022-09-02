<?php

namespace App\Http\Controllers\Api;

use App\Contracts\PostRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\Post\ShowResource;
use App\Http\Resources\Post\TopResource;

class PostController extends Controller
{

    public function __construct(
        protected PostRepositoryInterface $repository
    ) {
    }

    public function show($id)
    {
        $post = $this->repository->get($id);

        if ($post === null) {
            abort(404);
        }

        return new ShowResource($post);
    }

    public function top()
    {
        $posts = $this->repository->top();

        return TopResource::collection($posts);
    }
}
