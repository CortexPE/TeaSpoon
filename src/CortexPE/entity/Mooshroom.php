<?php

namespace CortexPE\entity;

use CortexPE\item\enchantment\Enchantment;
use CortexPE\Player;
use pocketmine\entity\Animal;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;

class Mooshroom extends Animal {
    const NETWORK_ID = self::MOOSHROOM;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;

    public function getName(): string {
        return "Mooshroom";
    }

    public function getDrops(): array{
		$lootingL = 0;
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$dmg = $cause->getDamager();
			if($dmg instanceof Player){
				$lootingL = $dmg->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING)->getLevel();
			}
		}
		return [
			Item::get(Item::RAW_BEEF, 0, mt_rand(1, 3 + $lootingL)),
			Item::get(Item::LEATHER, 0, mt_rand(0, 2 + $lootingL)),
		];
	}
}