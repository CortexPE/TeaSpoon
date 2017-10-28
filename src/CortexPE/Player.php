<?php

declare(strict_types = 1);

namespace CortexPE;

use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\Player as PMPlayer;

class Player extends PMPlayer {
	public function sendDimensionChange(int $dimension): bool{
		$pk = new ChangeDimensionPacket();
		$pk->dimension = $dimension;
		$this->dataPacket($pk);

		return true;
	}
}