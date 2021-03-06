<?php


namespace OGSoft\LaravelOpenApiValueObject;


use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Contracts\RequestBodyFactoryInterface;

class RequestBodyFactory extends AbstractFactory implements RequestBodyFactoryInterface
{
	use Referencable;

	public function build(): RequestBody
	{
		$schemas = $this->getSchema($this->data, 5);

		$schema = null;

		// if objectId is root use this attribute as root schema
		if (sizeof($schemas) == 1 && ($schemas[0]->objectId == "root")
		) {
			$schema = $schemas[0];
		} else {
			$schema = Schema::object()->properties(
				...$schemas
			);
		}

		return RequestBody::create()->content(MediaType::json()->schema($schema));
	}

}