<?php

namespace CortexPE\entity;

use pocketmine\entity\Animal;
use pocketmine\item\Item;

class Ocelot extends Animal {
    const NETWORK_ID = self::OCELOT;

	const TYPE_WILD = 0;
	const TYPE_TUXEDO = 1;
	const TYPE_TABBY = 2;
	const TYPE_SIAMESE = 3;

	public $width = 0.312;
	public $length = 2.188;
	public $height = 0.75;

    public function getName(): string {
        return "Mule";
    }

	public function getDrops(): array{
		return [
			Item::get(Item::LEATHER, 0, mt_rand(1, 2)),
		];
	}

	// TODO: Cat Types
}