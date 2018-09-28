<?php

namespace App;

abstract class StaticEndpoint implements IEndpoint
{
	/** @var Service */
	public static $app;

	public static function init( Service $app ): string
	{
		self::$app = $app;
		return self::class;
	}
}