<?php

namespace CortexPE\entity;

use pocketmine\entity\Animal;
use pocketmine\item\Item;

class Mule extends Animal {
    const NETWORK_ID = self::MULE;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 0;

    public function getName(): string {
        return "Mule";
    }

    public function initEntity(){
		$this->setMaxHealth(20);
		parent::initEntity();
	}

	public function getDrops(): array{
		return [
			Item::get(Item::LEATHER, 0, mt_rand(1, 2)),
		];
	}
}