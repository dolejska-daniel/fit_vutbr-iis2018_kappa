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
	const PARAM_ARRAY = "array";
	const PARAM_ARRAY_INT_KEYS = "array/keys:ints";
	const PARAM_ARRAY_STRING_KEYS = "array/keys:strings";
	const PARAM_ARRAY_INT_VALUES = "array/values:ints";
	const PARAM_ARRAY_STRING_VALUES = "array/values:strings";
	const PARAM_ARRAY_INT_INT = "array/keys:ints/values:ints";
	const PARAM_ARRAY_INT_STRING = "array/keys:ints/values:strings";
	const PARAM_ARRAY_STRING_INT = "array/keys:strings/values:ints";
	const PARAM_ARRAY_STRING_STRING = "array/keys:strings/values:strings";


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
	public function processRequest( string $path = null ): void
	{
		/** @var $httpRequest \Nette\Http\Request */
		global $httpRequest;

		if ($path[0] === "/" && strlen($path) > 1)
		{
			$path = preg_replace_callback("/(\/.)/", function( $x ) { return "\\" . strtoupper($x[1][1]); }, $path);
			$path = preg_replace_callback("/(\-.)/", function( $x ) { return strtoupper($x[1][1]); }, $path);
			$path = substr($path, 1);
		}
		else
		{
			$path = "Info";
		}
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
	 * @param $variable
	 * @param $name
	 *
	 * @return mixed
	 *
	 * @throws InvalidParameterException
	 */
	protected function processParam($datatype, $variable, $name )
	{
		if ($datatype === self::PARAM_INT)
		{
			if (is_numeric($variable) == false)
				throw new InvalidParameterException("Parameter '$name' is not valid integer.");

			if ($variable != (int)$variable)
				throw new InvalidParameterException("Parameter '$name' is not valid integer representation.");

			return (int)$variable;
		}
		elseif ($datatype === self::PARAM_INT_POS)
		{
			$variable = $this->processParam(self::PARAM_INT, $variable, $name);

			if ($variable < 0)
				throw new InvalidParameterException("Parameter '$name' is not valid positive integer.");

			return $variable;
		}
		elseif ($datatype === self::PARAM_INT_POS_NONZERO)
		{
			$variable = $this->processParam(self::PARAM_INT_POS, $variable, $name);

			if ($variable <= 0)
				throw new InvalidParameterException("Parameter '$name' is not valid positive nonzero integer.");

			return $variable;
		}
		elseif ($datatype === self::PARAM_FLOAT)
		{
			if (is_numeric($variable) == false)
				throw new InvalidParameterException("Parameter '$name' is not valid float.");

			if ($variable != (float)$variable)
				throw new InvalidParameterException("Parameter '$name' is not valid float representation.");

			return (float)$variable;
		}
		elseif ($datatype === self::PARAM_DATETIME)
		{
			try
			{
				$variable = DateTime::createFromFormat("Y-m-d H:i:s", $variable);
				return $variable;
			}
			catch (\Exception $ex)
			{
				throw new InvalidParameterException("Parameter '$name' is not valid datetime representation.");
			}
		}
		elseif ($datatype === self::PARAM_RGB)
		{
			if (preg_match("^(0|[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])?(,(0|[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])?){2}$", $variable) == false)
				throw new InvalidParameterException("Parameter '$name' is not valid RGB representation.");

			return $variable;
		}
		elseif ($datatype === self::PARAM_ARRAY)
		{
			if (is_array($variable) == false)
				throw new InvalidParameterException("Parameter '$name' is not valid array.");

			return $variable;
		}
		elseif ($datatype === self::PARAM_ARRAY_INT_KEYS)
		{
			$variable = $this->processParam(self::PARAM_ARRAY, $variable, $name);

			array_walk($variable, function ($value, $index, $name) {
				if (is_integer($index) == false)
					throw new InvalidParameterException("Key '$index' in array '$name' is not integer.");
			}, $name);

			return $variable;
		}
		elseif ($datatype === self::PARAM_ARRAY_STRING_KEYS)
		{
			$variable = $this->processParam(self::PARAM_ARRAY, $variable, $name);

			array_walk($variable, function ($value, $index, $name) {
				if (is_integer($index) == false)
					throw new InvalidParameterException("Key '$index' in array '$name' is not string.");
			}, $name);

			return $variable;
		}
		elseif ($datatype === self::PARAM_ARRAY_INT_VALUES)
		{
			$variable = $this->processParam(self::PARAM_ARRAY, $variable, $name);

			array_walk($variable, function (&$value, $index, $data) {
				try
				{
					$value = $this->getParam($data["var"], $index, self::PARAM_INT);
				}
				catch (InvalidParameterException $ex)
				{
					throw new InvalidParameterException("Value for '$index' key in array '$data[name]' is not integer.");
				}
			}, [ "var" => $variable, "name" => $name]);

			return $variable;
		}
		elseif ($datatype === self::PARAM_ARRAY_STRING_VALUES)
		{
			$variable = $this->processParam(self::PARAM_ARRAY, $variable, $name);

			array_walk($variable, function ($value, $index, $name) {
				if (is_string($value) == false)
					throw new InvalidParameterException("Value for '$index' key in array '$name' is not string.");
			}, $name);

			return $variable;
		}
		elseif ($datatype === self::PARAM_ARRAY_INT_INT)
		{
			$variable = $this->processParam(self::PARAM_ARRAY_INT_KEYS, $variable, $name);
			$variable = $this->processParam(self::PARAM_ARRAY_INT_VALUES, $variable, $name);

			return $variable;
		}
		elseif ($datatype === self::PARAM_ARRAY_INT_STRING)
		{
			$variable = $this->processParam(self::PARAM_ARRAY_INT_KEYS, $variable, $name);
			$variable = $this->processParam(self::PARAM_ARRAY_STRING_VALUES, $variable, $name);

			return $variable;
		}
		elseif ($datatype === self::PARAM_ARRAY_STRING_INT)
		{
			$variable = $this->processParam(self::PARAM_ARRAY_STRING_KEYS, $variable, $name);
			$variable = $this->processParam(self::PARAM_ARRAY_INT_VALUES, $variable, $name);

			return $variable;
		}
		elseif ($datatype === self::PARAM_ARRAY_STRING_STRING)
		{
			$variable = $this->processParam(self::PARAM_ARRAY_STRING_KEYS, $variable, $name);
			$variable = $this->processParam(self::PARAM_ARRAY_STRING_VALUES, $variable, $name);

			return $variable;
		}
		elseif ($datatype === self::PARAM_STRING)
		{
			return $variable;
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