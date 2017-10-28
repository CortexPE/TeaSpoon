<?php

namespace CortexPE\item;

use pocketmine\item\Item;
use pocketmine\item\ProjectileItem;

class EnchantingBottle extends ProjectileItem {

	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::BOTTLE_O_ENCHANTING, $meta, $count, "Bottle o' Enchanting");
	}

	public function getProjectileEntityType(): string{
		return "EnchantingBottle";
	}

	public function getThrowForce(): float{
		return 1.1;
	}
}