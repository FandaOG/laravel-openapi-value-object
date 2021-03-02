<?php

namespace Examples\Carstore\OpenApi\RequestBodies;

class CarValueObject
{
	/**
	 * Car name
	 *
	 * This is a car name description...
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