<?php

abstract class StaticEndpoint implements IEndpoint
{
	public static $app;

	public static function init( Application $app ): string
	{
		self::$app = $app;
		return self::class;
	}
}