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
        protected PostRepositoryInterface $repository,
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
            $existentPost = $this->repository->get($post['id']);

            $message = 'Post ' . $post['id'] . ' ';

            if ($existentPost) {
                $postToUpdate = $this->preparePostToUpdate($existentPost, $post);
                $this->repository->update($existentPost['id'], $postToUpdate);
                $message .= 'updated';
            } else {
                $postToCreate = $this->preparePostToCreate($post);
                $this->repository->store($postToCreate);
                $message .= 'created';
            }

            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                $this->consoleOutput->write($message, true);
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
        return $this->client::get('https://jsonplaceholder.typicode.com/posts')
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