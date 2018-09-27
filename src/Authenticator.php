<?php

class Authenticator implements \Nette\Security\IAuthenticator
{
	private $db;

	public function __construct( Nette\Database\Context $db )
	{
		$this->db = $db;
	}


	/**
	 * Performs an authentication against e.g. database.
	 * and returns IIdentity on success or throws AuthenticationException
	 *
	 * @return \Nette\Security\IIdentity
	 *
	 * @throws \Nette\Security\AuthenticationException
	 */
	function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		//  TODO: Login user
		throw new \Nette\NotImplementedException();

		return null;
	}
}