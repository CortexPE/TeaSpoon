<?php

namespace CortexPE\entity;

use pocketmine\entity\Monster;
use pocketmine\item\Item;

class PolarBear extends Monster {
    const NETWORK_ID = self::POLAR_BEAR;

	public $width = 0.6;
	public $length = 0.9;
	public $height = 0;

    public function getName(): string {
        return "Polar Bear";
    }

	public function initEntity(){
		$this->setMaxHealth(30);
		parent::initEntity();
	}

    public function getDrops() : array {
		return [
			Item::get(Item::RAW_SALMON, 0, mt_rand(0, 2)),
			Item::get(Item::RAW_FISH, 0, mt_rand(0, 2))
		];
    }
}