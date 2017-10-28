<?php

declare(strict_types = 1);

namespace CortexPE\entity;

use pocketmine\entity\Animal;
use pocketmine\item\Item;

class Chicken extends Animal {
	const NETWORK_ID = self::CHICKEN;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 0;

	public function getName(): string{
		return "Chicken";
	}

	public function getDrops(): array{
		$drops = [
			Item::get(Item::FEATHER, 0, mt_rand(0, 2)),
			Item::get(Item::RAW_CHICKEN, 0, 1),
		];

		return $drops;
	}
}
