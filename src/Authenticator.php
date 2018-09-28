<?php

namespace App;

use AuthenticationException;
use Nette\Database\Context;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;

class Authenticator implements IAuthenticator
{
	private $db;

	public function __construct( Context $db )
	{
		$this->db = $db;
	}


	/**
	 * Performs an authentication against e.g. database.
	 * and returns IIdentity on success or throws AuthenticationException
	 *
	 * @param array $credentials
	 *
	 * @return \Nette\Security\IIdentity
	 *
	 * @throws AuthenticationException
	 */
	function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$query = $this->db->table("kis__kocky");
		$query->where("login", $username);
		$ucet = $query->fetch();

		if ($ucet == false)
			throw new AuthenticationException("User was not found.", self::IDENTITY_NOT_FOUND);

		if (password_verify($password, $ucet->heslo) == false)
			throw new AuthenticationException("Password is not valid.", self::INVALID_CREDENTIAL);

		return new Identity($ucet->kocka_id, $ucet->role, $ucet->toArray());
	}
}