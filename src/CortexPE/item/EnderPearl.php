<?php

namespace CortexPE\item;

use pocketmine\item\Item;
use pocketmine\item\ProjectileItem;

class EnderPearl extends ProjectileItem {

	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::ENDER_PEARL, $meta, $count, "Ender Pearl");
	}

	public function getProjectileEntityType(): string{
		return "EnderPearl";
	}

	public function getThrowForce(): float{
		return 1.1;
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize(): int{
		return 16;
	}

}