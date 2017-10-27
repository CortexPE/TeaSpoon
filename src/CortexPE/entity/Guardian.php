<?php

declare(strict_types=1);

namespace CortexPE\entity;

use pocketmine\entity\Animal;
use pocketmine\item\Item;

class Guardian extends Animal {
	const NETWORK_ID = self::GUARDIAN;

	public $width = 0.95;
	public $length = 0.95;
	public $height = 0;

	public function getName() : string{
		return "Guardian";
	}

	public function initEntity(){
		$this->setMaxHealth(30);
		parent::initEntity();
	}

	public function getDrops() : array {
		return [
			Item::get(Item::RAW_FISH, 0, mt_rand(1, 2)),
			Item::get(Item::PRISMARINE_SHARD, 0, mt_rand(0, 1))
		];
	}
}
