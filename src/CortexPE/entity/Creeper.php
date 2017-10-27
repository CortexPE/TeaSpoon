<?php

declare(strict_types=1);

namespace CortexPE\entity;

use pocketmine\entity\Monster;
use pocketmine\item\Item;

class Creeper extends Monster {
	const NETWORK_ID = self::CREEPER;

	public function getName() : string{
		return "Creeper";
	}

	public function getDrops() : array {
		if(mt_rand(1,10) < 3){
			return [Item::get(Item::GUNPOWDER, 0,  1)];
		}

		return [];
	}
}
