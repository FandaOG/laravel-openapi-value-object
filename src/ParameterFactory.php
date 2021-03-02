<?php


namespace OGSoft\LaravelOpenApiValueObject;


use Exception;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Contracts\ParametersFactoryInterface;

class ParameterFactory extends AbstractFactory implements ParametersFactoryInterface
{
	use Referencable;

	public function build(): array
	{
		var_dump($this->data);
		$schemas = $this->getSchema($this->data, 10);

		$out = [];

		foreach ($schemas as $schema) {
			if (!$schema instanceof Schema) {
				throw new Exception("Not Schema instance");
			}
			$par = Parameter::query()
				->name($schema->objectId)
				->description($schema->description)
				->required(!$schema->nullable)
				->schema($schema);

			$out[] = $par;
		}
		return $out;
	}
}