<?php

namespace App;

use Nette\Database\Context;

abstract class StaticEndpoint implements IEndpoint
{
	/** @var Service */
	public static $app;

	/** @var Context */
	public static $db;

	/**
	 * @param Service $app
	 * @return string
	 */
	public static function init( Service $app ): string
	{
		self::$app = $app;
		self::$db = $app->context;
		return self::class;
	}

	/** @var array */
	public static $related_permissions = [];
}