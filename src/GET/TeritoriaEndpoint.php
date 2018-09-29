<?php

namespace GET;

use App\Authorizator;
use App\StaticEndpoint;
use AuthorizationException;
use FetchExtension;
use InvalidParameterException;


class TeritoriaEndpoint extends StaticEndpoint
{
	/**
	 * @return array
	 */
	public static function initRequest()
	{
		return [
			FetchExtension::init(self::$app, __CLASS__, "kis__teritoria")
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
			$fetchExtension->addCondition("FK_zivot_id.FK_kocka_id", self::$app->user->getId());
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
