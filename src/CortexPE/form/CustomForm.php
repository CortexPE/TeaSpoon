<?php
declare(strict_types=1);

namespace CortexPE\form;

use CortexPE\form\element\CustomFormElement;
use CotexPE\form\Form;
use pocketmine\Player;
use pocketmine\utils\Utils;

abstract class CustomForm extends Form{

	/** @var CustomFormElement[] */
	private $elements;

	/**
	 * @param string              $title
	 * @param CustomFormElement[] $elements
	 */
	public function __construct(string $title, array $elements){
		assert(Utils::validateObjectArray($elements, CustomFormElement::class));

		parent::__construct($title);
		$this->elements = array_values($elements);
	}

	/**
	 * @return string
	 */
	public function getType() : string{
		return Form::TYPE_CUSTOM_FORM;
	}

	/**
	 * @param int $index
	 *
	 * @return CustomFormElement|null
	 */
	public function getElement(int $index) : ?CustomFormElement{
		return $this->elements[$index] ?? null;
	}

	/**
	 * @return CustomFormElement[]
	 */
	public function getAllElements() : array{
		return $this->elements;
	}

	public function onSubmit(Player $player) : ?Form{
		return null;
	}

	/**
	 * Called when a player closes the form without submitting it.
	 * @param Player $player
	 * @return Form|null a form which will be opened immediately (before queued forms) as a response to this form, or null if not applicable.
	 */
	public function onClose(Player $player) : ?Form{
		return null;
	}


	public function handleResponse(Player $player, $data) : ?Form{
		if($data === null){
			return $this->onClose($player);
		}

		if(is_array($data)){
			/** @var array $data */
			foreach($data as $index => $value){
				$this->elements[$index]->setValue($value);
			}

			return $this->onSubmit($player);
		}

		throw new \UnexpectedValueException("Expected array or NULL, got " . gettype($data));
	}

	public function serializeFormData() : array{
		return [
			"content" => $this->elements
		];
	}
}
