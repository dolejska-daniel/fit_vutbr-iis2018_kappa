<?php

namespace GET;

use App\Authorizator;
use App\StaticEndpoint;


class InfoEndpoint extends StaticEndpoint
{
	public static function processRequest(): array
	{
		$classesAll = get_declared_classes();
		$classes = preg_grep('/(GET|POST|PUT|DELETE)\\\.+/', $classesAll);

		$data["endpointClasses"] = array_values($classes);

		return $data;
	}

	public static $permissions = [
		"allow" => [
			Authorizator::ROLE_UNAUTHENTICATED,
		],
		"deny" => [],
	];
	public static function exportPermissions(): array { return [ __CLASS__ => self::$permissions ] + self::$related_permissions; }
}
