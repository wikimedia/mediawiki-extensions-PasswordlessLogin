<?php

namespace PasswordlessLogin\adapter;

use PasswordlessLogin\model\Device;
use PasswordlessLogin\model\DevicesRepository;
use User;
use Wikimedia\Rdbms\IResultWrapper;
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

		return $this->extractDevice( $result );
	}

	function remove( User $user ) {
		$dbw = $this->loadBalancer->getConnection( DB_MASTER );
		$dbw->delete( self::TABLE_NAME, [
			'device_user_id' => $user->getId(),
		] );
	}

	function save( Device $device ) {
		if ( $this->findByUserId( $device->getUserId() ) === null ) {
			$this->insertDevice( $device );
		} else {
			$this->updateDevice( $device );
		}
	}

	function findByPairToken( $pairToken ) {
		$dbr = $this->loadBalancer->getConnection( DB_REPLICA );
		$result =
			$dbr->select( self::TABLE_NAME, [ 'id', 'device_id', 'device_user_id' ],
				[ 'device_pair_token' => $pairToken ] );

		return $this->extractDevice( $result );
	}

	private function extractDevice( IResultWrapper $result ) {
		if ( $result->numRows() !== 1 ) {
			return null;
		}

		$device = $result->fetchObject();

		return new Device( (int)$device->device_user_id, $device->device_id, $device->id );
	}

	private function insertDevice( Device $device ) {
		$dbw = $this->loadBalancer->getConnection( DB_MASTER );
		$dbw->insert( self::TABLE_NAME, [
			'device_user_id' => $device->getUserId(),
			'device_id' => $device->getDeviceId(),
			'device_pair_token' => $device->getPairToken(),
		] );
	}

	private function updateDevice( Device $device ) {
		$dbw = $this->loadBalancer->getConnection( DB_MASTER );
		$dbw->update( self::TABLE_NAME, [
			'device_id' => $device->getDeviceId(),
			'device_pair_token' => null,
		], [
			'device_user_id' => $device->getUserId(),
		] );
	}
}
