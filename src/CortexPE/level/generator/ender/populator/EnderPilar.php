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
		if(mt_rand(0, 100) <= 10){
			$this->level = $level;
			$amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
			for($i = 0; $i < $amount; ++$i){
				$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
				$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
				$y = $this->getHighestWorkableBlock($x, $z);
				if($this->level->getBlockIdAt($x, $y, $z) == Block::END_STONE){
					$height = mt_rand(28, 50);
					for($ny = $y; $ny < $y + $height; $ny++){
						for($r = 0.5; $r < 5; $r += 0.5){
							$nd = 360 / (2 * pi() * $r);
							for($d = 0; $d < 360; $d += $nd){
								$level->setBlockIdAt(intval($x + (cos(deg2rad($d)) * $r)),intval($ny),intval($z + (sin(deg2rad($d)) * $r)), Block::OBSIDIAN);
							}
						}
					}
				}
			}
		}
	}


	private function getHighestWorkableBlock($x, $z){
		for($y = 127; $y >= 0; --$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b == Block::END_STONE){
				break;
			}
		}

		return $y === 0 ? -1 : $y;
	}
}