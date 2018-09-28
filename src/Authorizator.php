<?php

namespace App;

use Nette\Security\IAuthorizator;
use Nette\Security\Permission;


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

	public function __construct()
	{
		$this->p = $p = new Permission();

		foreach (self::$roles as $role => $parent)
			$p->addRole($role);
	}

	/**
	 * Imports all required endpoint resources and permissions.
	 *
	 * @param \ReflectionClass $endpoint
	 */
	function importEndpoint(\ReflectionClass $endpoint)
	{
		$res = $endpoint->getMethod("exportResources")->invoke(null);
		foreach ($res as $resource)
			if ($this->p->hasResource($resource) == false)
				$this->p->addResource($resource);

		foreach ($endpoint->getMethod("exportPermissions")->invoke(null)["allow"] as $resource => $role)
			$this->p->allow($role, is_numeric($resource) ? $res : $resource);

		foreach ($endpoint->getMethod("exportPermissions")->invoke(null)["deny"] as $resource => $role)
			$this->p->deny($role, is_numeric($resource) ? $res : $resource);
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