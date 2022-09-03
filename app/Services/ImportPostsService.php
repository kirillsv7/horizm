<?php

namespace App\Services;

use App\Contracts\PostRepositoryInterface;
use App\Models\Post;
use App\Services\Calculators\PostBodyRatingCalculator;
use App\Services\Calculators\PostRatingCalculator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportPostsService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected PostRatingCalculator $ratingCalculator,
        protected Http $client,
        protected int $limit = 50,
    ) {
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }

    public function getPosts(string $url): array
    {
        return $this->client::get($url)->json();
    }

    public function limitPosts($posts): array
    {
        return array_slice($posts, 0, $this->getLimit());
    }

    public function preparePostToCreate(array $post): array
    {
        $postToCreate = [];

        foreach ($post as $column => $value) {
            $postToCreate[Str::snake($column)] = $value;
        }

        $postToCreate['rating'] = $this->ratingCalculator->calculate($postToCreate);

        return $postToCreate;
    }

    public function preparePostToUpdate(Post $existentPost, array $post): array
    {
        $postToUpdate = $existentPost->toArray();

        $postToUpdate['body'] = $post['body'];

        $postToUpdate['rating'] = $this->ratingCalculator->calculate($postToUpdate);

        return $postToUpdate;
    }

    public function handlePost(array $post): string
    {
        $existentPost = $this->postRepository->get($post['id']);

        if ($existentPost) {
            $postToUpdate = $this->preparePostToUpdate($existentPost, $post);
            $this->postRepository->update($existentPost['id'], $postToUpdate);

            return 'updated';
        }

        $postToCreate = $this->preparePostToCreate($post);
        $this->postRepository->store($postToCreate);

        return 'created';
    }
}