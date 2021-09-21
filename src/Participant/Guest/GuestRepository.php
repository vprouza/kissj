<?php

declare(strict_types=1);

namespace kissj\Participant\Guest;

use kissj\Orm\Repository;

/**
 * @table participant
 */
class GuestRepository extends Repository
{
    /**
     * @return Guest[]
     */
    public function findAll(): array
    {
        $guestsOnly = [];
        foreach (parent::findAll() as $participant) {
            if (! ($participant instanceof Guest)) {
                continue;
            }

            $guestsOnly[] = $participant;
        }

        return $guestsOnly;
    }
}
