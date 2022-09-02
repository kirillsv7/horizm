<?php

namespace Tests\Unit;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Services\ImportUsersService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ImportUsersServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @see ImportUsersService::getUser()
     */
    public function testGetUser()
    {
        $service = app()->make(ImportUsersService::class);
        $user    = $service->getUser(1);

        $this->assertIsArray($user);
    }

    /**
     * @test
     * @see ImportUsersService::handle()
     */
    public function testHandle()
    {
        Artisan::call('import:posts', [
            '--no-interaction' => true,
        ]);

        $service = app()->make(ImportUsersService::class);
        $service->handle();

        $userRepository = app()->make(UserRepositoryInterface::class);

        $users = $userRepository->all();

        $this->assertInstanceOf(User::class, $users->random());
    }
}
