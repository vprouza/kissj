<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddParticipantRegistrationCloseDate extends AbstractMigration
{
    public function up(): void
    {
        $participant = $this->table('participant');
        $participant
            ->addColumn('registration_close_date', 'datetime', ['default' => null, 'null' => true])
            ->save();

        $this->execute('
            UPDATE participant
            SET participant.registration_close_date = participant.updated_at
            FROM participant
            JOIN user ON user.id = participant.user_id
            WHERE user.status IN ("closed", "approved", "paid");
        ');
    }

    public function down(): void
    {
        $participant = $this->table('participant');
        $participant
            ->removeColumn('registration_close_date')
            ->save();
    }
}
