<?php

class Authorizator implements \Nette\Security\IAuthorizator
{

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
		// TODO: Implement isAllowed() method.
		throw new \Nette\NotImplementedException();

		return false;
	}
}