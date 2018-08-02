<?php


declare(strict_types=1);

namespace CortexPE\form\element;

/**
 * Represents a UI on/off switch. The switch may have a default value.
 */
class Toggle extends CustomFormElement{
	/** @var bool */
	private $default;
	/** @var bool */
	private $value;

	public function __construct(string $text, bool $defaultValue = false){
		parent::__construct($text);
		$this->default = $defaultValue;
	}

	public function getType() : string{
		return "toggle";
	}

	/**
	 * @return bool
	 */
	public function getDefaultValue() : bool{
		return $this->default;
	}

	/**
	 * @return bool
	 */
	public function getValue() : bool{
		return $this->value;
	}

	/**
	 * @param bool $value
	 *
	 * @throws \TypeError
	 */
	public function setValue($value) : void{
		if(!is_bool($value)){
			throw new \TypeError("Expected bool, got " . gettype($value));
		}

		$this->value = $value;
	}


	public function serializeElementData() : array{
		return [
			"default" => $this->default
		];
	}

}
