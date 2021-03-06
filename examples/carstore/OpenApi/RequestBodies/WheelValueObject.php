<?php

namespace Examples\Carstore\OpenApi\RequestBodies;

class WheelValueObject
{
	/**
	 * @var int|null
	 */
	protected ?int $size;

	/**
	 * @var TireValueObject|null
	 */
	protected ?TireValueObject $tire;

	/**
	 * @return int|null
	 */
	public function getSize(): ?int
	{
		return $this->size;
	}

	/**
	 * @param int|null $size
	 * @return WheelValueObject
	 */
	public function setSize(?int $size): WheelValueObject
	{
		$this->size = $size;
		return $this;
	}

	/**
	 * @return TireValueObject|null
	 */
	public function getTire(): ?TireValueObject
	{
		return $this->tire;
	}

	/**
	 * @param TireValueObject|null $tire
	 * @return WheelValueObject
	 */
	public function setTire(?TireValueObject $tire): WheelValueObject
	{
		$this->tire = $tire;
		return $this;
	}


}