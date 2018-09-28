<?php

namespace GET\Kocky;

use App\Authorizator;
use App\StaticEndpoint;


class HostiteleEndpoint extends StaticEndpoint
{
	public static function processRequest(): array
	{
		return [];
	}

	public static $resources = [
		__CLASS__
	];
	public static function exportResources(): array { return self::$resources; }

	public static $permissions = [
		"allow" => [
			Authorizator::ROLE_USER,
		],
		"deny" => [],
	];
	public static function exportPermissions(): array { return self::$permissions; }
}