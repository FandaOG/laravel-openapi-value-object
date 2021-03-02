<?php

namespace Examples\Carstore\OpenApi\Responses;

class CarResponse
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
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return CarResponse
	 */
	public function setName(string $name): CarResponse
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
	 * @return CarResponse
	 */
	public function setEnginePower(int $enginePower): CarResponse
	{
		$this->enginePower = $enginePower;
		return $this;
	}


}