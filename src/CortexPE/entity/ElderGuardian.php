<?php

declare(strict_types=1);

namespace CortexPE\entity;

use pocketmine\entity\Animal;
use pocketmine\item\Item;

class ElderGuardian extends Animal {
	const NETWORK_ID = self::ELDER_GUARDIAN;

	public $width = 1.45;
	public $length = 1.45;
	public $height = 0;

	public function getName() : string{
		return "Elder Guardian";
	}

	public function initEntity(){
		$this->setMaxHealth(80);
		parent::initEntity();
	}

	public function getDrops() : array {
		return [
			Item::get(Item::PRISMARINE_CRYSTALS, 0, mt_rand(0, 1)),
			Item::get(Item::PRISMARINE_SHARD, 0, mt_rand(0, 2))
		];
	}
}
