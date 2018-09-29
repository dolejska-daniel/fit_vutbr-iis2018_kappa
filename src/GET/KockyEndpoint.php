<?php

namespace GET;

use App\Authorizator;
use App\StaticEndpoint;
use AuthorizationException;
use FetchExtension;
use InvalidParameterException;


class KockyEndpoint extends StaticEndpoint
{
	/**
	 * @return array
	 */
	public static function initRequest()
	{
		//  Bridge permissions
		self::$related_permissions += RasyEndpoint::exportPermissions();

		//  Initiate request
		RasyEndpoint::initRequest();

		return [
			FetchExtension::init(self::$app, __CLASS__, "kis__kocky")
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
		if (self::$app->user->isInRole(Authorizator::ROLE_USER))
			$fetchExtension->addCondition("kocka_id", self::$app->user->getId());

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
