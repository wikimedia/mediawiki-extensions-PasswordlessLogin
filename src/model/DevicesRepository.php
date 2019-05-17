<?php

namespace PasswordlessLogin\model;

use User;

interface DevicesRepository {
	const SERVICE_NAME = 'PasswordlessLogin.DevicesRepository';

	/**
	 * @param Device $device
	 * @return void
	 */
	function save( Device $device );

	/**
	 * @param $userId
	 * @return Device|null
	 */
	function findByUserId( $userId );

	/**
	 * @param $pairToken
	 * @return Device|null
	 */
	function findByPairToken( $pairToken );

	/**
	 * @param User $user
	 * @return void
	 */
	function remove( User $user );
}
