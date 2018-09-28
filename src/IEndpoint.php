<?php

namespace App;


interface IEndpoint
{
	public static function init( Service $app ): string;

	public static function processRequest(): array;

	public static function exportResources(): array;
	public static function exportPermissions(): array;
}