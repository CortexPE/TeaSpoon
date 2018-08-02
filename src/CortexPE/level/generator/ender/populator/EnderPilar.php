<?php

/*
 *
 *  _____            _               _____
 * / ____|          (_)             |  __ \
 *| |  __  ___ _ __  _ ___ _   _ ___| |__) | __ ___
 *| | |_ |/ _ \ '_ \| / __| | | / __|  ___/ '__/ _ \
 *| |__| |  __/ | | | \__ \ |_| \__ \ |   | | | (_) |
 * \_____|\___|_| |_|_|___/\__, |___/_|   |_|  \___/
 *                         __/ |
 *                        |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author GenisysPro
 * @link https://github.com/GenisysPro/GenisysPro
 *
 *
*/

declare(strict_types = 1);

namespace CortexPE\level\generator\ender\populator;

use pocketmine\block\Block;
use pocketmine\level\{
	ChunkManager, generator\populator\Populator
};
use pocketmine\utils\Random;

class EnderPilar extends Populator {
	private const radii = [3, 4, 3, 5, 3, 4, 3, 3, 5, 4, 5, 3, 5, 4, 4, 5, 5, 4, 4, 4, 5];
	/** @var ChunkManager */
	private $level;
	private $randomAmount;
	private $baseAmount;

	public function setRandomAmount($amount){
		$this->randomAmount = $amount;
	}

	public function setBaseAmount($amount){
		$this->baseAmount = $amount;
	}

	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		// todo: only spawn within 50 blocks from spawn point at a circle (Usual Amount: 10-15 [in my pov])
		if(mt_rand(0, 100) <= 50){
			$this->level = $level;
			$x = $random->nextRange(0, 15);
			$z = $random->nextRange(0, 15);
			$height = mt_rand(76, 103);
			$radius = self::radii[array_rand(self::radii)];
			for($ny = 0; $ny < $height; $ny++){
				for($r = ($radius / 10); $r < $radius; $r += ($radius / 10)){
					$nd = 360 / (2 * pi() * $r);
					for($d = 0; $d < 360; $d += $nd){
						$level->setBlockIdAt(intval($x + (cos(deg2rad($d)) * $r)), intval($ny), intval($z + (sin(deg2rad($d)) * $r)), Block::OBSIDIAN);
					}
				}
			}
			if(mt_rand(1, 2) == 1){
				if($radius == 3){
					$bradius = 1;
				}else{
					$bradius = 2;
				}
				for($bx = -$bradius; $bx <= $bradius; $bx++){
					for($by = -$bradius; $by <= $bradius; $by++){
						for($bz = -$bradius; $bz <= $bradius; $bz++){
							$edge = (
									($bx == $bradius || $bx == -$bradius) &&
									($bz == $bradius || $bz == -$bradius)
								) || ($by == $bradius || $by == -$bradius);
							if($edge){
								$level->setBlockIdAt($x + $bx, ($height + 1) + $by, $z + $bz, Block::IRON_BARS);
							}
						}
					}
				}
			}
			$level->setBlockIdAt($x, $height, $z, Block::BEDROCK);
			$level->setBlockIdAt($x, $height + 1, $z, Block::AIR);
		}
	}
}