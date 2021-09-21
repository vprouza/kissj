<?php

declare(strict_types=1);

namespace Tests\Functional;

use DateTime;
use kissj\Participants\Patrol\PatrolService;
use kissj\User\RoleService;
use kissj\User\UserService;

use function assert;

use const DATE_ISO8601;

class PatrolLeaderTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testCreatePatrolLeader(): void
    {
        $app         = $this->app();
        $userService = $app->getContainer()->get('userService');
        assert($userService instanceof UserService);
        $patrolService = $app->getContainer()->get('patrolService');
        assert($patrolService instanceof PatrolService);
        $roleService = $app->getContainer()->get('roleService');
        assert($roleService instanceof RoleService);

        $email        = 'test4@example.com';
        $user         = $userService->registerUser($email);
        $patrolLeader = $patrolService->getPatrolLeader($user);
        $role         = $roleService->getRole($user);

        $this->assertEquals($patrolLeader->user->id, $user->id);
        $this->assertNotEquals('closed', $role->status);
    }

    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testFillRegistration(): void
    {
        $app         = $this->app();
        $userService = $app->getContainer()->get('userService');
        assert($userService instanceof UserService);
        $patrolService = $app->getContainer()->get('patrolService');
        assert($patrolService instanceof PatrolService);
        $roleService = $app->getContainer()->get('roleService');
        assert($roleService instanceof RoleService);

        $email        = 'test3@example.com';
        $user         = $userService->registerUser($email);
        $patrolLeader = $patrolService->getPatrolLeader($user);
        $role         = $roleService->getRole($user);

        $this->assertEquals($patrolLeader->user->id, $user->id);
        $this->assertEquals('open', $role->status);

        $patrolService->editPatrolLeaderInfo(
            $patrolLeader,
            'leader',
            'leaderový',
            'burákové máslo',
            (new DateTime())->format(DATE_ISO8601),
            'Kalimdor',
            'Azeroth',
            'attack helicopter',
            'Northrend',
            'High Elves',
            'none',
            'test@test.moe',
            'trolls',
            'some',
            'some note',
            'my great patrol'
        );

        $role = $roleService->getRole($user);
        $this->assertEquals('open', $role->status);
        $patrolService->closeRegistration($patrolLeader);
        $role = $roleService->getRole($user);
        $this->assertEquals('closed', $role->status);
    }

    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testAddPatrolParticipant(): void
    {
        $app         = $this->app();
        $userService = $app->getContainer()->get('userService');
        assert($userService instanceof UserService);
        $patrolService = $app->getContainer()->get('patrolService');
        assert($patrolService instanceof PatrolService);

        $email        = 'test5@example.com';
        $user         = $userService->registerUser($email);
        $patrolLeader = $patrolService->getPatrolLeader($user);

        $participant = $patrolService->addPatrolParticipant($patrolLeader);
        $this->assertEquals($patrolLeader->id, $participant->patrolLeader->id);
    }
}
