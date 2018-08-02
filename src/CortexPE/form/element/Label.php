<?php


declare(strict_types=1);

namespace CortexPE\form\element;

/**
 * Element which displays some text on a form.
 */
class Label extends CustomFormElement{

	public function getType() : string{
		return "label";
	}

	public function getValue(){
		return null;
	}

	public function setValue($value) : void{
		assert($value === null);
	}

	public function serializeElementData() : array{
		return [];
	}

}
