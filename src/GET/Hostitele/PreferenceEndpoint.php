<?php

namespace GET\Hostitele;

use App\Authorizator;
use App\StaticEndpoint;
use AuthorizationException;
use FetchExtension;
use GET\HostiteleEndpoint;
use GET\RasyEndpoint;
use InvalidParameterException;


class PreferenceEndpoint extends StaticEndpoint
{
	/**
	 * @return array
	 */
	public static function initRequest()
	{
		//  Bridge permissions
		self::$related_permissions += HostiteleEndpoint::exportPermissions();
		self::$related_permissions += RasyEndpoint::exportPermissions();

		//  Initiate request
		HostiteleEndpoint::initRequest();
		RasyEndpoint::initRequest();

		return [
			FetchExtension::init(self::$app, __CLASS__, "kis__hostitele_preference")
		];
	}

	/**
	 * @return array
	 *
	 * @throws InvalidParameterException
	 * @throws AuthorizationException
	 */
	public static function processRequest(): array
	{
		/** @var $fetchExtension FetchExtension */
		list($fetchExtension) = self::initRequest();
		/*
		if (self::$app->user->isInRole(Authorizator::ROLE_USER))
			$fetchExtension->addCondition("FK_rasa_id", ...);
		*/

		$data = $fetchExtension->fetchData();
		return $data;
	}

	public static $permissions = [
		"allow" => [
			Authorizator::ROLE_USER => Authorizator::ALL,
		],
		"deny" => [],
	];
	public static function exportPermissions(): array { return [ __CLASS__ => self::$permissions ] + self::$related_permissions; }
}
