<?php

/*
 *     __						    _
 *    / /  _____   _____ _ __ _   _| |
 *   / /  / _ \ \ / / _ \ '__| | | | |
 *  / /__|  __/\ V /  __/ |  | |_| | |
 *  \____/\___| \_/ \___|_|   \__, |_|
 *						      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author LeverylTeam
 * @link https://github.com/LeverylTeam
 *
*/

declare(strict_types = 1);

namespace CortexPE\block;

use CortexPE\Main;
use pocketmine\block\{Air, Block, Fallable};
use pocketmine\item\Item;
use pocketmine\level\{Position, sound\GenericSound};
use pocketmine\math\Vector3;
use pocketmine\Player;

class DragonEgg extends Fallable {

	/** @var int $id */
	protected $id = self::DRAGON_EGG;

	/**
	 * DragonEgg constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return "Dragon Egg";
	}

	/**
	 * @return float
	 */
	public function getHardness(): float{
		return 4.5;
	}

	/**
	 * @return float
	 */
	public function getBlastResistance(): float{
		return 45;
	}

	/**
	 * @return int
	 */
	public function getLightLevel(): int{
		return 1;
	}

	/**
	 * @param Item $item
	 * @return bool
	 */
	public function isBreakable(Item $item): bool{
		return false;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated(): bool{
		return true;
	}

	/**
	 * @param Item $item
	 * @param Player|null $player
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null): bool{
		if(Main::$dragonEggTeleport){
			$level = $this->getLevel();
			for($c = 0; $c <= 16; $c++){
				$x = $this->getX() + mt_rand(-15, 15);
				$y = $this->getY() + mt_rand(-7, 7);
				$z = $this->getZ() + mt_rand(-15, 15);

				if($level->getBlockIdAt($x, $y, $z) == Block::AIR && $level->isInWorld($x, $y, $z)) {
					$level->setBlock($this, new Air(), true, true);
					$oldPos = $this->asVector3();
					$level->setBlock(($pos = new Vector3($x, $y, $z)), $this, true, true);
					$posDelta = $pos->subtract($oldPos);
					$dist = $oldPos->distance($pos);
					for($c = 0; $c <= $dist; $c++) {
						$progress = $c / $dist;
						$this->getLevel()->addSound(new GenericSound(new Position($oldPos->x + $posDelta->x * $progress,
							1.62 + $oldPos->y + $posDelta->y * $progress, $oldPos->z + $posDelta->z * $progress,
							$this->getLevel()), 2010));
					}

					return true;
				}
			}
		}

		return true;
	}
}
