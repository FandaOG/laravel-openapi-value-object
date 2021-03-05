# laravel-openapi-value-object

This library extends [vyuldashev/laravel-openapi](https://github.com/vyuldashev/laravel-openapi). Main idea is do not
create a scheme manually:

```php
class UserCreateRequestBody extends RequestBodyFactory
{
    public function build(): RequestBody
    {
        return RequestBody::create('UserCreate')
            ->description('User data')
            ->content(
                MediaType::json()->schema(UserSchema::ref())
            );
    }
}
```

The library get any PHP class and schema is autogenerated from class attributes. You should use PHP doc:

```php
class CarRequest
{
	/**
	 * Car name <-- auto parsed title
	 *
	 * This is a car name description... <-- auto parsed description 
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Engine power in kW
	 *
	 * More is better
	 *
	 * @var int
	 */
	protected int $enginePower;

	/**
	 * Wheels of car
	 *
	 * It is necessary to move
	 * @var WheelValueObject[]
	 */
	protected array $wheels;

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return CarValueObject
	 */
	public function setName(string $name): CarValueObject
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getEnginePower(): int
	{
		return $this->enginePower;
	}

	/**
	 * @param int $enginePower
	 * @return CarValueObject
	 */
	public function setEnginePower(int $enginePower): CarValueObject
	{
		$this->enginePower = $enginePower;
		return $this;
	}

	/**
	 * @return WheelValueObject[]
	 */
	public function getWheels(): array
	{
		return $this->wheels;
	}

	/**
	 * @param WheelValueObject[] $wheels
	 * @return CarValueObject
	 */
	public function setWheels(WheelValueObject ...$wheels): CarValueObject
	{
		$this->wheels = $wheels;
		return $this;
	}
}
```

Output is:

```php
(...)
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
(...)
```

# Usage

Check `examples/cartore/CarController.php` controller and `exmples/carstore/OpenApi` directory.

# TODO

Update `composer.json` after [pull request](https://github.com/vyuldashev/laravel-openapi/pull/37) is accepted.

# Changes

- 2021-03-05 - support `@example`