<?php

namespace PasswordlessLogin\adapter;

use PasswordlessLogin\model\Device;
use PasswordlessLogin\model\DevicesRepository;
use User;
use Wikimedia\Rdbms\LoadBalancer;

class DatabaseDeviceRepository implements DevicesRepository {
	const TABLE_NAME = 'passwordlesslogin_devices';
	private $loadBalancer;

	public function __construct( LoadBalancer $loadBalancer ) {
		$this->loadBalancer = $loadBalancer;
	}

	function findByUserId( $userId ) {
		$dbr = $this->loadBalancer->getConnection( DB_REPLICA );
		$result =
			$dbr->select( self::TABLE_NAME, [ 'id', 'device_id', 'device_user_id' ],
				[ 'device_user_id' => $userId ] );

		if ($result->numRows() !== 1) {
			return null;
		}

		$device = $result->fetchObject();

		return new Device( (int)$device->device_user_id, $device->device_id, $device->id );
	}

	function remove( User $user ) {
		$dbw = $this->loadBalancer->getConnection(DB_MASTER);
		$dbw->delete(self::TABLE_NAME, [
			'device_user_id' => $user->getId()
		]);
	}

	function save( Device $device ) {
		$dbw = $this->loadBalancer->getConnection( DB_MASTER );
		$dbw->insert( self::TABLE_NAME, [
			'device_user_id' => $device->getDeviceUserId(),
			'device_id' => $device->getDeviceId(),
		] );
	}
}
