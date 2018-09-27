<?php

use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Http\UserStorage;
use Nette\Neon\Neon;
use Nette\Security\User;


/**
 * Class Application
 */
class Application
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
	 * @return Application
	 */
	public static function init( string $target ): self
	{
		$db_config = Neon::decode(file_get_contents(CONFIG_DIR . "/$target.db.neon"));

		if (self::$_instance === null)
			self::$_instance = new Application(array_values($db_config));

		return self::$_instance;
	}


	/**
	 * @param string $path
	 *
	 * @throws BadRequestException
	 * @throws InternalException
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

		$this->storage = $s = new Nette\Caching\Storages\FileStorage(CACHE_DIR);
		$structure = new \Nette\Database\Structure($c, $s);
		$this->context = $db = new Context($c, $structure);

		$this->authenticator = new Authenticator($db);
		$this->authorizator = new Authorizator();

		$us = new UserStorage($session);
		$this->user = new User($us, $this->authenticator, $this->authorizator);
	}
}