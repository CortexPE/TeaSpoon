<?php

declare(strict_types = 1);

namespace CortexPE\entity;

use CortexPE\Player;
use pocketmine\entity\Human;
use pocketmine\entity\Monster;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;

class Spider extends Monster {
	const NETWORK_ID = self::SPIDER;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.9;

	public function getName(): string{
		return "Spider";
	}

	public function getDrops(): array{
		$drops = [Item::get(Item::STRING, 0, 1)];
		if($this->lastDamageCause instanceof EntityDamageByEntityEvent and ($this->lastDamageCause->getEntity() instanceof Player || $this->lastDamageCause->getEntity() instanceof Human)){
			if(mt_rand(0, 199) < 5){
				switch(mt_rand(0, 2)){
					case 0:
						$drops[] = Item::get(Item::IRON_INGOT, 0, 1);
						break;
					case 1:
						$drops[] = Item::get(Item::CARROT, 0, 1);
						break;
					case 2:
						$drops[] = Item::get(Item::POTATO, 0, 1);
						break;
				}
			}
		}

		return $drops;
	}
}
