<?php

namespace Examples\Carstore\OpenApi\Parameters;

class CarCreateParams
{
	/**
	 * param id
	 *
	 * param id desc
	 *
	 * @var int
	 */
	public int $id;

	/**
	 * @var string
	 */
	public string $type;

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return CarCreateParams
	 */
	public function setId(int $id): CarCreateParams
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @return CarCreateParams
	 */
	public function setType(string $type): CarCreateParams
	{
		$this->type = $type;
		return $this;
	}

}