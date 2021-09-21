<?php

declare(strict_types=1);

namespace kissj\Participant\Patrol;

use kissj\Orm\Repository;

/**
 * @table participant
 */
class PatrolLeaderRepository extends Repository
{
    /**
     * @return PatrolLeader[]
     */
    public function findAll(): array
    {
        $patrolLeadersOnly = [];
        foreach (parent::findAll() as $participant) {
            if (! ($participant instanceof PatrolLeader)) {
                continue;
            }

            $patrolLeadersOnly[] = $participant;
        }

        return $patrolLeadersOnly;
    }
}
