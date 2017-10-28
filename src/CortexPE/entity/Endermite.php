<?php

namespace CortexPE\entity;

use pocketmine\entity\Monster;

class Endermite extends Monster {
	const NETWORK_ID = self::ENDERMITE;

	public function getName(): string{
		return "Endermite";
	}
}