<?php

declare(strict_types = 1);

namespace CortexPE\entity;

use pocketmine\entity\Monster;

class Enderman extends Monster {
	const NETWORK_ID = self::ENDERMAN;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;

	public function getName(): string{
		return "Enderman";
	}
}
