<?php

namespace OGSoft\LaravelOpenApiValueObject\Tests;

use Examples\Carstore\CarController;
use Illuminate\Support\Facades\Route;


class CarstoreTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		Route::post('/car', [CarController::class, 'create']);
	}

	public function testGenerate(): void
	{
		$spec = $this->generate()->toArray();

		// request body
		self::assertEquals([
			'type' => 'object',
			'properties' =>
				[
					'name' =>
						[
							'title' => 'Car name',
							'description' => 'This is a car name description...',
							'example' => 'My best car, Skoda Octavia, M1A1',
							'type' => 'string',
							'nullable' => false,
						],
					'enginePower' =>
						[
							'title' => 'Engine power in kW',
							'description' => 'More is better',
							'type' => 'integer',
							'nullable' => false,
						],
					'wheels' =>
						[
							'title' => 'Wheels of car',
							'description' => 'It is necessary to move',
							'type' => 'array',
							'items' =>
								[
									'type' => 'object',
									'properties' =>
										[
											'size' =>
												[
													'type' => 'integer',
													'nullable' => true,
												],
											'tire' =>
												[
													'type' => 'object',
													'properties' =>
														[
															'winter' =>
																[
																	'type' => 'boolean',
																	'nullable' => false,
																],
														],
												],
										],
								],
							'nullable' => false,
						],
				]
		], $spec['paths']['/car']['post']['requestBody']['content']['application/json']['schema']);

		// parameters
		self::assertEquals([
				[
					'name' => 'id',
					'in' => 'query',
					'description' => 'param id desc',
					'required' => true,
					'schema' =>
						[
							'title' => 'param id',
							'description' => 'param id desc',
							'type' => 'integer',
							'nullable' => false,
						]
				],
				[
					'name' => 'type',
					'in' => 'query',
					'required' => true,
					'schema' =>
						[
							'type' => 'string',
							'nullable' => false,
						]
				]
			]
			, $spec['paths']['/car']['post']['parameters']
		);

		// response
		self::assertEquals([
				'type' => 'object',
				'properties' =>
					[
						'name' =>
							[
								'title' => 'Car name',
								'description' => 'This is a car name description...',
								'type' => 'string',
								'nullable' => false,
							],
						'enginePower' =>
							[
								'title' => 'Engine power in kW',
								'description' => 'More is better',
								'type' => 'integer',
								'nullable' => false,
							],
					],
			]
			, $spec['paths']['/car']['post']['responses']['default']['content']['application/json']['schema']);
	}
}
