<?php

namespace Tests\Unit;

use App\Contracts\PostRepositoryInterface;
use App\Services\ImportPostsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportPostsServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @see ImportPostsService::getPostsFromApi()
     */
    public function testGetPostsFromApi()
    {
        $service = app()->make(ImportPostsService::class);
        $posts   = $service->getPostsFromApi(env('API_POSTS_URL'));

        $this->assertIsArray($posts);
    }

    /**
     * @test
     * @see ImportPostsService::limitPosts()
     */
    public function testLimitPosts()
    {
        $service = app()->make(ImportPostsService::class);
        $service->setLimit(20);
        $posts = $service->getPostsFromApi(env('API_POSTS_URL'));

        $this->assertCount($service->getLimit(), $posts);
    }

    /**
     * @test
     * @see ImportPostsService::preparePostToCreate()
     */
    public function testPreparePostToCreate()
    {
        $service      = app()->make(ImportPostsService::class);
        $posts        = $service->getPostsFromApi(env('API_POSTS_URL'));
        $post         = $posts[array_rand($posts)];
        $preparedPost = $service->preparePostToCreate($post);

        $this->assertArrayHasKey('id', $preparedPost);
        $this->assertArrayHasKey('user_id', $preparedPost);
        $this->assertArrayHasKey('title', $preparedPost);
        $this->assertArrayHasKey('body', $preparedPost);
        $this->assertArrayHasKey('rating', $preparedPost);
    }

    /**
     * @test
     * @see ImportPostsService::preparePostToUpdate()
     */
    public function testPreparePostToUpdate()
    {
        $oldPostData = [
            'id'      => 1,
            'user_id' => 2,
            'title'   => 'Test post title',
            'body'    => 'Test post description',
            'rating'  => 9,
        ];

        $postRepository = app()->make(PostRepositoryInterface::class);
        $oldPost        = $postRepository->store($oldPostData);

        $service     = app()->make(ImportPostsService::class);
        $newPostData = $service->getPostsFromApi(env('API_POSTS_URL'))[0];
        $updatedPost = $service->preparePostToUpdate($oldPost, $newPostData);

        $this->assertEquals($oldPostData['id'], $updatedPost['id']);
        $this->assertEquals($oldPostData['user_id'], $updatedPost['user_id']);
        $this->assertEquals($oldPostData['title'], $updatedPost['title']);
        $this->assertNotEquals($oldPostData['body'], $updatedPost['body']);
        $this->assertNotEquals($oldPostData['rating'], $updatedPost['rating']);
    }

    /**
     * @test
     * @see ImportPostsService::handlePost()
     */
    public function testHandlePost()
    {
        $service      = app()->make(ImportPostsService::class);
        $posts        = $service->getPostsFromApi(env('API_POSTS_URL'));
        $post         = $posts[array_rand($posts)];
        $preparedPost = $service->preparePostToCreate($post);
        $created      = $service->handlePost($preparedPost);
        $updated      = $service->handlePost($preparedPost);

        $this->assertEquals('created', $created);
        $this->assertEquals('updated', $updated);
    }
}
