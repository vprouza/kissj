<?php

declare(strict_types=1);

namespace kissj\User;

use kissj\Orm\EntityDatetime;

/**
 * @property int     $id
 * @property string  $token
 * @property User    $user    m:hasOne
 * @property bool $used
 */
class LoginToken extends EntityDatetime
{
}
