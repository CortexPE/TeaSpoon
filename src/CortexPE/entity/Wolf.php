<?php

namespace CortexPE\entity;

use pocketmine\entity\Animal;

class Wolf extends Animal {
	const NETWORK_ID = self::WOLF;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;

	public function getName(): string{
		return "Wolf";
	}
}