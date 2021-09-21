<?php

declare(strict_types=1);

namespace Tests\Functional;

use kissj\Participants\Patrol\PatrolService;
use kissj\User\UserService;

use function assert;

class CreatePatrolLeaderTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testRegisterAndLogin(): void
    {
        $app         = $this->app();
        $userService = $app->getContainer()->get('userService');
        assert($userService instanceof UserService);
        $patrolService = $app->getContainer()->get('patrolService');
        assert($patrolService instanceof PatrolService);

        $email        = 'test2@example.com';
        $user         = $userService->registerUser($email);
        $patrolLeader = $patrolService->getPatrolLeader($user);

        $this->assertEquals($patrolLeader->user->id, $user->id);
    }
}
