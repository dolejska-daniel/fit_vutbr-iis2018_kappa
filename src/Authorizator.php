<?php

namespace App;

use Nette\Security\IAuthorizator;
use Nette\Security\Permission;
use ReflectionClass;


class Authorizator implements IAuthorizator
{
	const ROLE_UNAUTHENTICATED = 'guest';
	const ROLE_USER = 'uzivatel';
	const ROLE_ADMIN = 'commander';

	static $roles = [
		self::ROLE_UNAUTHENTICATED => null,
		self::ROLE_USER     => null,
		'authenticated'     => self::ROLE_USER,
		self::ROLE_ADMIN    => self::ROLE_USER,
	];


	/** @var Permission */
	private $p;

	/** @var array */
	private $tableResources = [];


	/**
	 * Authorizator constructor.
	 */
	public function __construct()
	{
		$this->p = $p = new Permission();

		foreach (self::$roles as $role => $parent)
			$p->addRole($role);
	}

	/**
	 * Imports all required endpoint resources and permissions.
	 *
	 * @param ReflectionClass $endpoint
	 */
	function importEndpoint( ReflectionClass $endpoint )
	{
		foreach ($endpoint->getMethod("exportPermissions")->invoke(null) as $resource => $data)
		{
			if ($this->p->hasResource($resource) == false)
				$this->p->addResource($resource);

			foreach ($data["allow"] as $role => $privilege)
				$this->p->allow($role, $resource, $privilege);

			foreach ($data["deny"] as $role => $privilege)
				$this->p->deny($role, $resource, $privilege);
		}
	}

	/**
	 * @param ReflectionClass $endpoint
	 * @param string $table
	 */
	function addEndpointTable( ReflectionClass $endpoint, string $table )
	{
		$this->tableResources[$table][explode("\\", $endpoint->getNamespaceName())[0]] = $endpoint->name;
	}

	/**
	 * @param string $table
	 * @return array
	 */
	function getResourceByTable( string $table ): array
	{
		return @$this->tableResources[$table] ?: [];
	}

	/**
	 * Performs a role-based authorization.
	 *
	 * @param  string|null
	 * @param  string|null
	 * @param  string|null
	 *
	 * @return bool
	 */
	function isAllowed($role, $resource, $privilege)
	{
		return $this->p->isAllowed($role, $resource, $privilege);
	}
}