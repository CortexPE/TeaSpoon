<?php

declare(strict_types = 1);

namespace CortexPE\entity;

use pocketmine\entity\Animal;

class Ghast extends Animal {
	const NETWORK_ID = self::GHAST;

	public $width = 6;
	public $length = 6;
	public $height = 6;

	public function getName(): string{
		return "Ghast";
	}

	public function initEntity(){
		$this->setMaxHealth(10);
		parent::initEntity();
	}
}
