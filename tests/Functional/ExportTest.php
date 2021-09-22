<?php

declare(strict_types=1);

namespace Tests\Functional;

use DateTime;
use kissj\Export\ExportService;
use kissj\Participants\Patrol\PatrolService;
use kissj\User\RoleService;
use kissj\User\UserService;

use function assert;
use function sprintf;

use const DATE_ISO8601;

class ExportTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testExportMedicalData(): void
    {
        $app           = $this->app();
        $exportService = $app->getContainer()->get('exportService');
        assert($exportService instanceof ExportService);
        $userService = $app->getContainer()->get('userService');
        assert($userService instanceof UserService);
        $patrolService = $app->getContainer()->get('patrolService');
        assert($patrolService instanceof PatrolService);
        $roleService = $app->getContainer()->get('roleService');
        assert($roleService instanceof RoleService);

        for ($i = 0; $i < 10; $i++) {
            $email        = 'test-' . $i . '@example.com';
            $user         = $userService->registerUser($email);
            $patrolLeader = $patrolService->getPatrolLeader($user);
            $patrolService->editPatrolLeaderInfo(
                $patrolLeader,
                sprintf('leader%s', $i),
                'leaderový',
                'burákové máslo' . $i,
                (new DateTime())->format(DATE_ISO8601),
                'Kalimdor',
                'Azeroth',
                'attack helicopter',
                'Northrend',
                'High Elves',
                'none',
                'test' . $i . '@test.moe',
                'trolls',
                'some',
                'some note',
                'my great patrol'
            );
        }

        $rows = $exportService->medicalDataToCSV('cej2018');

        $this->assertCount(11, $rows);

        $this->assertSame(null, $rows[0][0]);
        $this->assertSame(null, $rows[0][1]);
        $this->assertSame('leader0', $rows[1][0]);
        $this->assertSame('leaderový', $rows[1][1]);
        $this->assertSame('leader1', $rows[2][0]);
        $this->assertSame('leaderový', $rows[2][1]);
    }
}
