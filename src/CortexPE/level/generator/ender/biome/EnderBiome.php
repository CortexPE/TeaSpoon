<?php

declare(strict_types = 1);

namespace CortexPE\level\generator\ender;

use pocketmine\level\generator\biome\Biome;

class EnderBiome extends Biome {

	public function getName(): string{
		return "Ender";
	}
}