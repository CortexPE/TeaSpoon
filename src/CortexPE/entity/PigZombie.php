<?php

namespace CortexPE\entity;

use CortexPE\item\enchantment\Enchantment;
use pocketmine\entity\Monster;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\Player;

class PigZombie extends Monster {
	const NETWORK_ID = self::ZOMBIE_PIGMAN;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 1.8;

	public function getName(): string{
		return "Zombie Pigman";
	}

	public function getDrops(): array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING)->getLevel();
				$drops = [
					Item::get(Item::GOLD_NUGGET, 0, mt_rand(0, 1 + $lootingL)),
					Item::get(Item::ROTTEN_FLESH, 0, mt_rand(0, 1 + $lootingL)),
				];

				if(mt_rand(1, 200) <= (5 + 2 * $lootingL)){
					$drops[] = Item::get(Item::GOLD_INGOT, 0, 1);
				}

				return $drops;
			}
		}

		return [];
	}
}