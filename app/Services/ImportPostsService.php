<?php

namespace App\Services;

use App\Contracts\PostRepositoryInterface;
use App\Models\Post;
use App\Services\Calculators\PostBodyRatingCalculator;
use App\Services\Calculators\PostRatingCalculator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;

class ImportPostsService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected PostRatingCalculator $ratingCalculator,
        protected Http $client,
        protected ConsoleOutput $consoleOutput,
        protected int $limit = 50,
    ) {
    }

    public function handle()
    {
        $posts = $this->getPosts();
        $posts = $this->limitPosts($posts);

        foreach ($posts as $post) {
            $existentPost = $this->postRepository->get($post['id']);

            if ($existentPost) {
                $postToUpdate = $this->preparePostToUpdate($existentPost, $post);
                $this->postRepository->update($existentPost['id'], $postToUpdate);
                $status = 'updated';
            } else {
                $postToCreate = $this->preparePostToCreate($post);
                $this->postRepository->store($postToCreate);
                $status = 'created';
            }

            if (app()->runningInConsole()) {
                $this->consoleOutput->write(sprintf('Post %1$d %2$s', $post['id'], $status), true);
            }
        }
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }

    public function getPosts(): array
    {
        return $this->client::get(env('API_POSTS_URL'))
                            ->json();
    }

    protected function limitPosts($posts)
    {
        return array_slice($posts, 0, $this->getLimit());
    }

    protected function preparePostToCreate(array $post): array
    {
        $postToCreate = [];

        foreach ($post as $column => $value) {
            $postToCreate[Str::snake($column)] = $value;
        }

        $postToCreate['rating'] = $this->ratingCalculator->calculate($postToCreate);

        return $postToCreate;
    }

    protected function preparePostToUpdate(Post $existentPost, array $post): array
    {
        $postToUpdate = $existentPost->toArray();

        $postToUpdate['body'] = $post['body'];

        $postToUpdate['rating'] = $this->ratingCalculator->calculate($postToUpdate);

        return $postToUpdate;
    }

}