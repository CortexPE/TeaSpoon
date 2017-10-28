<?php

namespace CortexPE\entity;

use pocketmine\entity\Monster;

class SnowGolem extends Monster {
	const NETWORK_ID = self::SNOW_GOLEM;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;

	public function getName(): string{
		return "Snow Golem";
	}

	public function initEntity(){
		$this->setMaxHealth(4);
		parent::initEntity();
	}
}