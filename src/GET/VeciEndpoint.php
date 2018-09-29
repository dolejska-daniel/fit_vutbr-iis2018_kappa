<?php

namespace GET;

use App\Authorizator;
use App\StaticEndpoint;
use AuthorizationException;
use FetchExtension;
use InvalidParameterException;


class VeciEndpoint extends StaticEndpoint
{
	/**
	 * @return array
	 */
	public static function initRequest()
	{
		//  Bridge permissions
		self::$related_permissions += TeritoriaEndpoint::exportPermissions();

		//  Initiate request
		TeritoriaEndpoint::initRequest();

		return [
			FetchExtension::init(self::$app, __CLASS__, "kis__veci")
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
			$fetchExtension->addCondition("FK_teritorium_id", ...);
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
