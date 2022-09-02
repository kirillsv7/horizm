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
     * @see ImportPostsService::getPosts()
     */
    public function testGetPosts()
    {
        $service = app()->make(ImportPostsService::class);
        $posts   = $service->getPosts();

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

        $serviceReflection = new \ReflectionClass($service);
        $getPosts          = $serviceReflection->getMethod('getPosts');
        $limitPosts        = $serviceReflection->getMethod('limitPosts');
        $getPosts->setAccessible(true);
        $limitPosts->setAccessible(true);

        $posts = $getPosts->invoke($service);
        $posts = $limitPosts->invokeArgs($service, [$posts]);

        $this->assertCount($service->getLimit(), $posts);
    }

    /**
     * @test
     * @see ImportPostsService::preparePostToCreate()
     */
    public function testPreparePostToCreate()
    {
        $service = app()->make(ImportPostsService::class);
        $post    = $service->getPosts()[rand(0, $service->getLimit())];

        $serviceReflection   = new \ReflectionClass($service);
        $preparePostToCreate = $serviceReflection->getMethod('preparePostToCreate');
        $preparePostToCreate->setAccessible(true);

        $preparedPost = $preparePostToCreate->invokeArgs($service, [$post]);

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
        $newPostData = $service->getPosts()[0];

        $serviceReflection   = new \ReflectionClass($service);
        $preparePostToUpdate = $serviceReflection->getMethod('preparePostToUpdate');
        $preparePostToUpdate->setAccessible(true);

        $updatedPost = $preparePostToUpdate->invokeArgs($service, [$oldPost, $newPostData]);

        $this->assertEquals($oldPostData['id'], $updatedPost['id']);
        $this->assertEquals($oldPostData['user_id'], $updatedPost['user_id']);
        $this->assertEquals($oldPostData['title'], $updatedPost['title']);
        $this->assertNotEquals($oldPostData['body'], $updatedPost['body']);
        $this->assertNotEquals($oldPostData['rating'], $updatedPost['rating']);
    }

    /**
     * @test
     * @see ImportPostsService::handle()
     */
    public function testHandle()
    {
        $service = app()->make(ImportPostsService::class);
        $service->handle();

        $postRepository = app()->make(PostRepositoryInterface::class);

        $postsCount = $postRepository->all()->count();

        $this->assertEquals($service->getLimit(), $postsCount);
    }
}
