<?php

namespace PasswordlessLogin\model;

use User;

interface ChallengesRepository {
	const SERVICE_NAME = 'PasswordlessLogin.ChallengesRepository';

	public function save( Challenge $challenge );

	/**
	 * @param $challenge
	 * @return Challenge
	 */
	public function findByChallenge( $challenge );

	/**
	 * @param User $user
	 * @return Challenge
	 */
	public function findByUser( User $user );

	public function remove( User $user );
}
