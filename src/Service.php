<?php

namespace App;

use AuthorizationException;
use BadRequestException;
use InternalException;
use Nette\Caching\Storages\FileStorage;
use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Database\Structure;
use Nette\Http\UserStorage;
use Nette\Neon\Neon;
use Nette\Security\User;
use ReflectionClass;
use ReflectionException;


/**
 * Class Service
 */
class Service
{
	/** @var Connection */
	public $conn;

	/** @var Context */
	public $context;

	public $storage;

	public $user;

	public $authenticator;

	public $authorizator;


	/**
	 * @param string $target
	 * @return Service
	 */
	public static function init( string $target ): self
	{
		$db_config = Neon::decode(file_get_contents(CONFIG_DIR . "/$target.db.neon"));

		if (self::$_instance === null)
			self::$_instance = new Service(array_values($db_config));

		return self::$_instance;
	}


	/**
	 * @param string $path
	 *
	 * @throws BadRequestException
	 * @throws InternalException
	 * @throws AuthorizationException
	 */
	public function processRequest( string $path ): void
	{
		/** @var $httpRequest \Nette\Http\Request */
		global $httpRequest;

		$path = preg_replace_callback("/(\/.)/", function( $x ) { return "\\" . strtoupper($x[1][1]); }, $path);
		$path = preg_replace_callback("/(\-.)/", function( $x ) { return strtoupper($x[1][1]); }, $path);
		$path = substr($path, 1);
		$method = $httpRequest->getMethod();

		$className = "\\$method\\{$path}Endpoint";

		if (class_exists($className) == false)
			throw new BadRequestException("Requested endpoint class '$className' was not found.");

		try
		{
			$ref = new ReflectionClass($className);
			if ($ref->implementsInterface(IEndpoint::class) == false)
				throw new BadRequestException("Requested endpoint class '$className' does not implement IEndpoint interface.");

			$ref->getMethod("init")->invoke(null, $this);
			$this->authorizator->importEndpoint($ref);

			if ($this->user->isAllowed($ref->name) == false)
				throw new AuthorizationException("User not allowed to use '$ref->name'.");

			$this->data = $ref->getMethod("processRequest")->invoke(null);
		}
		catch (ReflectionException $ex)
		{
			throw new InternalException($ex);
		}
	}

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}


	private $data = [];

	private static $_instance;
	private function __construct( array $db_config )
	{
		global $session;

		$this->conn = $c = new Connection(...$db_config);

		$this->storage = $s = new FileStorage(CACHE_DIR);
		$structure = new Structure($c, $s);
		$this->context = $db = new Context($c, $structure);

		$this->authenticator = new Authenticator($db);
		$this->authorizator = new Authorizator();

		$us = new UserStorage($session);
		$this->user = new User($us, $this->authenticator, $this->authorizator);
	}
}