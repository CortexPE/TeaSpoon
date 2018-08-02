<?php

declare(strict_types=1);

namespace CortexPE\form\element;

class Dropdown extends CustomFormElement{
	/** @var int */
	protected $defaultOptionIndex;
	/** @var int|null */
	protected $selectedOption;
	/** @var string[] */
	protected $options;

	/**
	 * @param string   $text
	 * @param string[] $options
	 * @param int      $defaultOptionIndex
	 */
	public function __construct(string $text, array $options, int $defaultOptionIndex = 0){
		parent::__construct($text);
		$this->options = $options;

		if(!isset($this->options[$defaultOptionIndex])){
			throw new \InvalidArgumentException("No option at index $defaultOptionIndex, cannot set as default");
		}
		$this->defaultOptionIndex = $defaultOptionIndex;
	}

	public function getType() : string{
		return "dropdown";
	}

	/**
	 * @return int|null
	 */
	public function getValue() : ?int{
		return $this->selectedOption;
	}

	/**
	 * @param int $value
	 *
	 * @throws \TypeError
	 */
	public function setValue($value) : void{
		if(!is_int($value)){
			throw new \TypeError("Expected int, got " . gettype($value));
		}

		$this->selectedOption = $value;
	}

	/**
	 * Returns the text of the option at the specified index, or null if it doesn't exist.
	 *
	 * @param int $index
	 *
	 * @return string|null
	 */
	public function getOption(int $index) : ?string{
		return $this->options[$index] ?? null;
	}

	/**
	 * Returns the text of the selected option.
	 * @return string
	 */
	public function getSelectedOption() : string{
		$index = $this->getValue();
		if($index === null){
			throw new \InvalidStateException("No option selected (form closed or hasn't been submitted yet)");
		}

		$option = $this->getOption($index);

		if($option !== null){
			return $option;
		}

		throw new \InvalidStateException("No option found at index $index");
	}

	/**
	 * @return int
	 */
	public function getDefaultOptionIndex() : int{
		return $this->defaultOptionIndex;
	}

	/**
	 * @return string
	 */
	public function getDefaultOption() : string{
		return $this->options[$this->defaultOptionIndex];
	}

	/**
	 * @return string[]
	 */
	public function getOptions() : array{
		return $this->options;
	}


	public function serializeElementData() : array{
		return [
			"options" => $this->options,
			"default" => $this->defaultOptionIndex
		];
	}
}
