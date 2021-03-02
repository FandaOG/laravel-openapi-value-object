<?php

namespace Examples\Carstore;

use Examples\Carstore\OpenApi\Parameters\CarCreateParams;
use Examples\Carstore\OpenApi\RequestBodies\CarValueObject;
use Examples\Carstore\OpenApi\Responses\CarResponse;
use OGSoft\LaravelOpenApiValueObject\ParameterFactory;
use OGSoft\LaravelOpenApiValueObject\RequestBodyFactory;
use OGSoft\LaravelOpenApiValueObject\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class CarController
{
	/**
	 * Create car
	 */
	#[OpenApi\Operation('createCar')]
	// RequestBodyFactory is from repository, CarValueObject is defined by user
	#[OpenApi\RequestBody(RequestBodyFactory::class, CarValueObject::class)]
	// ParameterFactory is from repository, CarCreateParams is defined by user
	#[OpenApi\Parameters(ParameterFactory::class, CarCreateParams::class)]
	// ResponseFactory is from repository, CarResponse is defined by user
	#[OpenApi\Response(ResponseFactory::class, 200, null, CarResponse::class)]
	public function create()
	{
	}
}
