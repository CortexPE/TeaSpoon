<?php

declare(strict_types = 1);

namespace CortexPE\entity;

use CortexPE\item\enchantment\Enchantment;
use CortexPE\Player;
use pocketmine\entity\Animal;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;

class Donkey extends Animal {
	const NETWORK_ID = self::DONKEY;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 0;

	public function getName(): string{
		return "Donkey";
	}

	public function getDrops(): array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$looting = $damager->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING);
				if($looting !== null){
					$lootingL = $looting->getLevel();
				} else {
					$lootingL = 0;
				}
				$drops = [
					Item::get(Item::RAW_BEEF, 0, mt_rand(1, 3 + $lootingL)),
					Item::get(Item::LEATHER, 0, mt_rand(0, 2 + $lootingL)),
				];

				return $drops;
			}
		}

		return [];
	}
}
