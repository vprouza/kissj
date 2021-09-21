<?php

declare(strict_types=1);

namespace Tests\Functional;

use kissj\User\UserService;

use function assert;

class RegisterTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testRegisterAndLogin(): void
    {
        $app         = $this->app();
        $userService = $app->getContainer()->get('userService');
        assert($userService instanceof UserService);

        $email        = 'test@example.com';
        $user         = $userService->registerUser($email);
        $readableRole = 'Patrol Leader';
        $token        = $userService->sendLoginTokenByMail($email, $readableRole);
        $loadedUser   = $userService->getLoginTokenFromStringToken($token)->user;

        $this->assertEquals($user->id, $loadedUser->id);
    }
}
