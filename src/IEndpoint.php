<?php

interface IEndpoint
{
	public static function init( Application $app ): string;

	public static function processRequest(): array;
}