<?php

namespace PasswordlessLogin\model;

use User;

interface DevicesRepository {
	const SERVICE_NAME = 'PasswordlessLogin.DevicesRepository';

	function save( Device $device );

	function findByUserId( $userId );

	function remove( User $user );
}
