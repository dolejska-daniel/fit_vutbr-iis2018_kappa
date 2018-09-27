<?php

namespace GET;

use StaticEndpoint;


class InfoEndpoint extends StaticEndpoint
{
	public static function processRequest(): array
	{
		$classesAll = get_declared_classes();
		$classes = preg_grep('/(GET|POST|PUT|DELETE)\\\.+/', $classesAll);

		$data["endpointClasses"] = array_values($classes);

		return $data;
	}
}