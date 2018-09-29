<?php

namespace POST\Hostitele;

use App\Authorizator;
use App\StaticEndpoint;


class PreferenceEndpoint extends StaticEndpoint
{
	public static function processRequest(): array
	{
		return [];
	}

	public static $permissions = [
		"allow" => [
			Authorizator::ROLE_USER => Authorizator::ALL,
		],
		"deny" => [],
	];
	public static function exportPermissions(): array { return [ __CLASS__ => self::$permissions ] + self::$related_permissions; }
}
