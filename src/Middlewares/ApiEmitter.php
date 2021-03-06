<?php

namespace Contributte\Api\Middlewares;

use Contributte\Api\Dispatcher\IDispatcher;
use Contributte\Api\Exception\Logical\InvalidStateException;
use Contributte\Api\Http\ApiRequest;
use Contributte\Api\Http\ApiResponse;
use Exception;

class ApiEmitter
{

	/** @var IDispatcher */
	protected $dispatcher;

	/**
	 * @param IDispatcher $dispatcher
	 */
	public function __construct(IDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * MIDDLEWARE **************************************************************
	 */

	/**
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @param callable $next
	 * @return ApiResponse
	 */
	public function __invoke(ApiRequest $request, ApiResponse $response, callable $next)
	{
		// Pass this API request/response objects to API dispatcher
		$response = $this->dispatch($request, $response);

		// Pass response to next middleware
		$response = $next($request, $response);

		return $response;
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @param ApiRequest $apiRequest
	 * @param ApiResponse $apiResponse
	 * @return ApiResponse
	 */
	protected function dispatch(ApiRequest $apiRequest, ApiResponse $apiResponse)
	{
		try {
			// Pass to dispatcher, find handler, process some logic and return response.
			$apiResponse = $this->dispatcher->dispatch($apiRequest, $apiResponse);

			// Validate returned api response
			if (!($apiResponse instanceof ApiResponse)) {
				throw new InvalidStateException(sprintf('Returned response must be type of %s', ApiResponse::class));
			}

			return $apiResponse;
		} catch (Exception $e) {
			// Just throw this out
			throw $e;
		}
	}

}
