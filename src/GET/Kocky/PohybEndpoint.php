<?php

namespace GET\Kocky;

use App\Authorizator;
use App\StaticEndpoint;
use AuthorizationException;
use FetchExtension;
use GET\TeritoriaEndpoint;
use GET\ZivotyEndpoint;
use InvalidParameterException;


class PohybEndpoint extends StaticEndpoint
{
	/**
	 * @return array
	 */
	public static function initRequest()
	{
		//  Bridge permissions
		self::$related_permissions += TeritoriaEndpoint::exportPermissions();
		self::$related_permissions += ZivotyEndpoint::exportPermissions();

		//  Initiate request
		TeritoriaEndpoint::initRequest();
		ZivotyEndpoint::initRequest();

		return [
			FetchExtension::init(self::$app, __CLASS__, "kis__kocky_pohyb")
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
			$fetchExtension->addCondition("FK_zivot_id.FK_kocka_id", self::$app->user->getId());

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
