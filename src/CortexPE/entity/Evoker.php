<?php

declare(strict_types = 1);

namespace CortexPE\entity;

use pocketmine\entity\Monster;

class Evoker extends Monster {
	const NETWORK_ID = self::EVOCATION_ILLAGER;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 0;

	public function getName(): string{
		return "Evoker";
	}

	public function initEntity(){
		$this->setMaxHealth(24);
		parent::initEntity();
	}
}
