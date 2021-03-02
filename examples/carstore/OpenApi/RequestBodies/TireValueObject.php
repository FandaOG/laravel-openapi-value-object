<?php

namespace Examples\Carstore\OpenApi\RequestBodies;

class TireValueObject
{
	/**
	 * @var bool
	 */
	protected bool $winter;

	/**
	 * @return bool
	 */
	public function isWinter(): bool
	{
		return $this->winter;
	}

	/**
	 * @param bool $winter
	 * @return TireValueObject
	 */
	public function setWinter(bool $winter): TireValueObject
	{
		$this->winter = $winter;
		return $this;
	}

}