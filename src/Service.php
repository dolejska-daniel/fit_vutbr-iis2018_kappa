<?php

namespace App;

use AuthenticationException;
use AuthorizationException;
use BadRequestException;
use InternalException;
use InvalidParameterException;
use MissingParameterException;
use Nette\Caching\Storages\FileStorage;
use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Database\Structure;
use Nette\Http\UserStorage;
use Nette\Neon\Neon;
use Nette\Security\User;
use Nette\Utils\DateTime;
use Nette\Utils\Validators;
use ReflectionClass;
use ReflectionException;


/**
 * Class Service
 */
class Service
{
	const PARAM_INT = "integer";
	const PARAM_INT_POS = "integer/positive";
	const PARAM_INT_POS_NONZERO = "integer/positive#nonzero";
	const PARAM_STRING = "string";
	const PARAM_DATETIME = "string/datetime";
	const PARAM_FLOAT = "float";
	const PARAM_RGB = "string/rgb";


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
	 * @throws MissingParameterException
	 * @throws InvalidParameterException
	 * @throws BadRequestException
	 * @throws InternalException
	 * @throws AuthorizationException
	 * @throws AuthenticationException
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
	 * @param $datatype
	 * @param $value
	 *
	 * @return mixed
	 *
	 * @throws InvalidParameterException
	 */
	protected function processParam( $datatype, $value, $name )
	{
		if ($datatype === self::PARAM_INT)
		{
			if (is_numeric($value) == false)
				throw new InvalidParameterException("Parameter '$name' is not valid integer.");

			if ($value != (int)$value)
				throw new InvalidParameterException("Parameter '$name' is not valid integer representation.");

			return (int)$value;
		}
		elseif ($datatype === self::PARAM_INT_POS)
		{
			$value = $this->processParam(self::PARAM_INT, $value, $name);

			if ($value < 0)
				throw new InvalidParameterException("Parameter '$name' is not valid positive integer.");

			return $value;
		}
		elseif ($datatype === self::PARAM_INT_POS_NONZERO)
		{
			$value = $this->processParam(self::PARAM_INT_POS, $value, $name);

			if ($value <= 0)
				throw new InvalidParameterException("Parameter '$name' is not valid positive nonzero integer.");

			return $value;
		}
		elseif ($datatype === self::PARAM_FLOAT)
		{
			if (is_numeric($value) == false)
				throw new InvalidParameterException("Parameter '$name' is not valid float.");

			if ($value != (float)$value)
				throw new InvalidParameterException("Parameter '$name' is not valid float representation.");

			return (float)$value;
		}
		elseif ($datatype === self::PARAM_DATETIME)
		{
			try
			{
				$value = DateTime::createFromFormat("Y-m-d H:i:s", $value);
				return $value;
			}
			catch (\Exception $ex)
			{
				throw new InvalidParameterException("Parameter '$name' is not valid datetime representation.");
			}
		}
		elseif ($datatype === self::PARAM_RGB)
		{
			if (preg_match("^(0|[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])?(,(0|[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])?){2}$", $value) == false)
				throw new InvalidParameterException("Parameter '$name' is not valid RGB representation.");

			return $value;
		}
		elseif ($datatype === self::PARAM_STRING)
		{
			return $value;
		}
		else
		{
			throw new InvalidParameterException("Parameter '$name' validated against unknown datatype.");
		}
	}

	/**
	 * @param array $source
	 * @param string $name
	 * @param string $datatype
	 * @param null $default
	 * @param bool $required
	 *
	 * @return mixed
	 *
	 * @throws MissingParameterException
	 * @throws InvalidParameterException
	 */
	protected function getParam(array $source, string $name, string $datatype, $default = null, bool $required = true )
	{
		$param = $default;
		if (isset($source[$name]))
			$param = $source[$name];
		elseif ($required)
			//  TODO: Throw argument exception
			throw new MissingParameterException("Required parameter '$name' was not found!");

		return $this->processParam($datatype, $param, $name);
	}

	public function getQueryParam( string $name, string $datatype, $default = null, bool $required = true )
	{
		return $this->getParam($_GET, $name, $datatype, $default, $required);
	}

	public function getPostParam( string $name, string $datatype, $default = null, bool $required = true )
	{
		return $this->getParam($_POST, $name, $datatype, $default, $required);
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