<?php

namespace CortexPE\entity;

use pocketmine\entity\Monster;

class Witch extends Monster {
	const NETWORK_ID = self::WITCH;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 0;

	public function getName(): string{
		return "Witch";
	}

	public function initEntity(){
		$this->setMaxHealth(26);
		parent::initEntity();
	}

	public function getDrops(): array{
		return [];
	}
}