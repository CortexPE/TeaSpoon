<?php

namespace CortexPE\entity;

use CortexPE\item\enchantment\Enchantment;
use pocketmine\entity\Animal;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\Player;

class Pig extends Animal {
	const NETWORK_ID = self::PIG;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 0;

	public function getName(): string{
		return "Pig";
	}

	public function getDrops(): array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING)->getLevel();

				return [
					Item::get(Item::RAW_PORKCHOP, 0, mt_rand(1, 3 + $lootingL)),
				];
			}
		}

		return [];
	}
}