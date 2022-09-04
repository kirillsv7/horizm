<?php

namespace Tests\Unit;

use App\Services\ImportUsersService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ImportUsersServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @see ImportUsersService::getUserFromApi()
     */
    public function testGetUserFromApi()
    {
        $service = app()->make(ImportUsersService::class);
        $user    = $service->getUserFromApi(1);

        $this->assertIsArray($user);
    }

    /**
     * @test
     * @see ImportUsersService::getUserIdsFromPosts()
     */
    public function testGetUserIdsFromPosts()
    {
        $service = app()->make(ImportUsersService::class);
        $userIds = $service->getUserIdsFromPosts();

        $this->assertEmpty($userIds);

        Artisan::call('import:posts', [
            '--no-interaction' => true,
        ]);

        $userIds = $service->getUserIdsFromPosts();

        $this->assertNotEmpty($userIds);
    }

    /**
     * @test
     * @see ImportUsersService::handleUser()
     */
    public function testHandleUser()
    {
        Artisan::call('import:posts', [
            '--no-interaction' => true,
        ]);

        $service = app()->make(ImportUsersService::class);
        $userIds = $service->getUserIdsFromPosts();

        foreach ($userIds as $id) {
            $created = $service->handleUser($id);

            $this->assertEquals('created', $created);
        }

        foreach ($userIds as $id) {
            $alreadyExists = $service->handleUser($id);

            $this->assertEquals('already exists', $alreadyExists);
        }
    }
}
