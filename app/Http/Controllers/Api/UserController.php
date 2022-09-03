<?php

namespace App\Http\Controllers\Api;

use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\IndexResource;

class UserController extends Controller
{

    public function __construct(
        protected UserRepositoryInterface $repository
    ) {
    }

    public function index()
    {
        $users = $this->repository->getOrderedByAvgPostsRating();

        return IndexResource::collection($users);
    }

}
