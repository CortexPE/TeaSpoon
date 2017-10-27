<?php

// Vindicators 3: The Return of Worldender

namespace CortexPE\entity;

use pocketmine\entity\Monster;
use pocketmine\item\Item;

class Vindicator extends Monster {
	const NETWORK_ID = self::VINDICATOR;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 0;

	public function getName(): string{
		return "Vindicator";
	}

	public function initEntity(){
		$this->setMaxHealth(24);
		parent::initEntity();
	}

	public function getDrops(): array{
		return [
			Item::get(Item::EMERALD, 0, mt_rand(0, 1))
		];
	}
}