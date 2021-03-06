<?php

namespace Contributte\Api\Schema\Serialization;

use Contributte\Api\Schema\ApiSchema;
use Contributte\Api\Schema\Endpoint;
use Contributte\Api\Schema\EndpointHandler;
use Contributte\Api\Schema\EndpointParameter;
use Contributte\Api\Schema\SchemaMapping;

final class ArrayHydrator implements IHydrator
{

	/**
	 * @param array $data
	 * @return ApiSchema
	 */
	public function hydrate($data)
	{
		$schema = new ApiSchema();

		foreach ($data as $route) {
			// @todo replace to EndpointHandler::factory()
			// move validation inside, make class immutable
			$handler = new EndpointHandler();
			$handler->setClass($route[SchemaMapping::HANDLER][SchemaMapping::HANDLER_CLASS]);
			$handler->setMethod($route[SchemaMapping::HANDLER][SchemaMapping::HANDLER_METHOD]);
			//$handler->setMethod($route[SchemaMapping::HANDLER][SchemaMapping::HANDLER_TYPE]);

			$endpoint = new Endpoint();
			$endpoint->setHandler($handler);
			$endpoint->setMethods($route[SchemaMapping::METHODS]);
			$endpoint->setMask($route[SchemaMapping::MASK]);
			$endpoint->setPattern($route[SchemaMapping::PATTERN]);

			foreach ($route[SchemaMapping::PARAMETERS] as $p) {
				$param = new EndpointParameter();
				$param->setName($p[SchemaMapping::PARAMETERS_NAME]);
				$param->setType(EndpointParameter::TYPE_SCALAR);
				$endpoint->addParameter($param);
			}

			$schema->addEndpoint($endpoint);
		}

		return $schema;
	}

}
