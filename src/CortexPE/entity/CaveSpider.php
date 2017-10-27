<?php

declare(strict_types=1);

namespace CortexPE\entity;

use pocketmine\entity\Monster;

class CaveSpider extends Monster {
	const NETWORK_ID = self::CAVE_SPIDER;

	public $width = 1;
	public $length = 1;
	public $height = 0.5;

	public function getName() : string{
		return "Cave Spider";
	}
}
