<?php

declare(strict_types = 1);

namespace CortexPE\entity;

use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;

class Horse extends Living {
	const NETWORK_ID = self::HORSE;

	public function getName(): string{
		return "Horse";
	}

	public function setChestPlate($id){
		/*
		416, 417, 418, 419 only
		*/
		$pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->slots = [
			Item::get(0, 0),
			Item::get($id, 0),
			Item::get(0, 0),
			Item::get(0, 0),
		];
		foreach($this->level->getPlayers() as $player){
			$player->dataPacket($pk);
		}
	}
}
