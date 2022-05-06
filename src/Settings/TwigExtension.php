<?php

declare(strict_types=1);

namespace kissj\Settings;

use kissj\Participant\Patrol\PatrolLeader;
use kissj\Participant\Troop\TroopLeader;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

class TwigExtension extends AbstractExtension
{
    /**
     * @return TwigTest[]
     */
    public function getTests(): array
    {
        return [
            new TwigTest('PatrolLeader', function ($participant): bool {
                return $participant instanceof PatrolLeader;
            }),
            new TwigTest('TroopLeader', function ($participant): bool {
                return $participant instanceof TroopLeader;
            }),
        ];
    }
}
