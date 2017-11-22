<?php

namespace kissj\Participant\Patrol;

use kissj\User\User;


interface PatrolServiceInterface {
	public function addPatrolLeaderInfo(PatrolLeader $patrolLeader,
										string $firstName,
										string $lastName,
										string $allergies,
										\DateTime $birthDate,
										string $birthPlace,
										string $country,
										string $gender,
										string $permanentResidence,
										string $scoutUnit,
										string $telephoneNumber,
										string $email,
										string $foodPreferences,
										string $cardPassportNumber,
										string $notes,
		// PatrolLeader specific
										string $patrolName
	);
	
	public function addParticipant(PatrolLeader $patrolLeader,
								   string $firstName,
								   string $lastName,
								   string $allergies,
								   \DateTime $birthDate,
								   string $birthPlace,
								   string $country,
								   string $gender,
								   string $permanentResidence,
								   string $scoutUnit,
								   string $telephoneNumber,
								   string $email,
								   string $foodPreferences,
								   string $cardPassportNumber,
								   string $notes);
	
	public function getPatrolLeader(User $user): PatrolLeader;
	
	public function closeRegistration(PatrolLeader $patrolLeader);
}