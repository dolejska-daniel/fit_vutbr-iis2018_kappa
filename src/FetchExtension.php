<?php

use App\Service;


/**
 * Class FetchExtension
 */
class FetchExtension
{
	/** @var Service */
	protected $app;

	/** @var \Nette\Database\Table\Selection */
	protected $query;

	/** @var string */
	protected $resource;

	/** @var string */
	protected $table;


	/**
	 * FetchExtension constructor.
	 *
	 * @param Service $app
	 * @param string $resource
	 * @param string $table
	 *
	 * @throws InternalException
	 */
	protected function __construct( Service $app, string $resource, string $table )
	{
		$this->app = $app;
		$this->resource = $resource;
		$this->query = $app->context->table($this->table = $table);

		try
		{
			$ref = new ReflectionClass($resource);
			//  Import endpoint permissions
			$app->authorizator->importEndpoint($ref);
			//  Create table-endpoint relation (link)
			$app->authorizator->addEndpointTable($ref, $table);
		}
		catch (ReflectionException $ex)
		{
			throw new InternalException("Failed to construct endpoint reflection for '$resource'.");
		}
	}

	/**
	 * @param Service $app
	 * @param string $resource
	 * @param string $table
	 *
	 * @return FetchExtension
	 */
	public static function init( Service $app, string $resource, string $table ): self
	{
		$self = new self($app, $resource, $table);
		return $self;
	}


	/**
	 * @param string $condition
	 * @param string $value
	 */
	public function addCondition( string $condition, string $value )
	{
		$this->query->where($condition, $value);
	}

	/**
	 * @param string $conditions
	 * @param string $fetchSingle
	 *
	 * @return mixed
	 *
	 * @throws AuthorizationException
	 * @throws InvalidParameterException
	 */
	public function fetchData( string $conditions = 'cond', string $fetchSingle = 'fetchSingle' )
	{
		$conds = $this->app->getQueryParam($conditions, $this->app::PARAM_ARRAY_STRING_STRING, [], false);
		foreach ($conds as $condition => $value)
		{
			//  Validate and apply all SQL WHERE conditions
			list($all, $subfield, $field, $operation) = $this->app->validateDatabaseCondition($condition);

			$fieldspecs = "";
			$resource = $this->resource;
			if ($subfield)
			{
				//  Field is from different table (throught SQL JOIN)
				//  Get foreign keys from the table
				$refs = $this->app->structure->getBelongsToReference($this->table);
				//  Get resource name based on related table name
				$resource = $this->app->authorizator->getResourceByTable($refs[$subfield])[explode("\\", $this->resource)[0]];

				$fieldspecs = " referenced";
			}

			//  Validate permissions
			if ($this->app->user->isAllowed($resource, $field) == false)
				throw new AuthorizationException("Unauthorized condition access to$fieldspecs '$field' ($all) column on '$resource' resource.");

			//  Add WHERE condition
			$this->query->where($condition, $value);
		}

		$fetchSingle = $this->app->getQueryParam($fetchSingle, $this->app::PARAM_BOOL, false, false);
		if ($fetchSingle)
			return $this->query->fetch()->toArray();

		return $this->query->fetchAssoc("[]");
	}
}