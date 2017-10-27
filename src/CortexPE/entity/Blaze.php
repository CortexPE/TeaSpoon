<?php

namespace CortexPE\entity;

use CortexPE\item\enchantment\Enchantment;
use pocketmine\entity\Monster;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;

class Blaze extends Monster {
    const NETWORK_ID = self::BLAZE;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;

    public function getName(): string {
        return "Blaze";
    }

	public function getDrops() : array {
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING)->getLevel();
				$drops = [Item::get(Item::BLAZE_ROD, 0, mt_rand(0, 1 + $lootingL))];

				return $drops;
			}
		}

		return [];

	}
}