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

use pocketmine\block\{
    Air, Fallable
};
use pocketmine\item\Item;
use pocketmine\level\{
    Level, Position, sound\GenericSound
};
use pocketmine\Player;

class DragonEgg extends Fallable {

	const RAND_VERTICAL = [-7, -6, -5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7];
	const RAND_HORIZONTAL = [-15, -14, -13, -12, -11, -10, -9, -8, -7, -6, -5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];

	/** @var int $id */
	protected $id = self::DRAGON_EGG;

	/**
	 * DragonEgg constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0) {
		$this->meta = $meta;
	}

    /**
     * @return string
     */
	public function getName() : string{
		return "Dragon Egg";
	}

    /**
     * @return float
     */
	public function getHardness() : float{
		return 4.5;
	}

    /**
     * @return float
     */
	public function getResistance() : float{
		return 45;
	}

    /**
     * @return int
     */
	public function getLightLevel() : int{
		return 1;
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isBreakable(Item $item) : bool{
		return false;
	}

    /**
     * @return bool
     */
	public function canBeActivated() : bool{
		return true;
	}

    /**
     * @param Item $item
     * @param Player|null $player
     * @return bool
     */
	public function onActivate(Item $item, Player $player = null) : bool{
	    /** @var bool $safe */
		$safe = false;
		while (!$safe) {
			$level = $this->getLevel();
			$x = $this->getX() + self::RAND_HORIZONTAL[array_rand(self::RAND_HORIZONTAL)];
			$y = $this->getY() + self::RAND_VERTICAL[array_rand(self::RAND_VERTICAL)];
			$z = $this->getZ() + self::RAND_HORIZONTAL[array_rand(self::RAND_HORIZONTAL)];
			if ($level->getBlockIdAt($x, $y, $z) == 0 && $y < Level::Y_MAX) {
				return ($safe = true);
			}
		}
		/** @noinspection PhpUndefinedVariableInspection */
		$level->setBlock($this, new Air(), true, true);
		$oldpos = clone $this;
		/** @noinspection PhpUndefinedVariableInspection */
		/** @noinspection PhpUndefinedVariableInspection */
		/** @noinspection PhpUndefinedVariableInspection */
		$pos = new Position($x, $y, $z, $level);
		$newpos = clone $pos;
		$level->setBlock($pos, $this, true, true);
		/** @var Position $posdistance */
		$posdistance = new Position($newpos->x - $oldpos->x, $newpos->y - $oldpos->y, $newpos->z - $oldpos->z, $this->getLevel());
		/** @var int $intdistance */
		$intdistance = $oldpos->distance($newpos);
		for ($c = 0; $c <= $intdistance; $c++) {
		    /** @var int $progress */
			$progress = $c / $intdistance;
			$this->getLevel()->addSound(new GenericSound(new Position($oldpos->x + $posdistance->x * $progress, 1.62 + $oldpos->y + $posdistance->y * $progress, $oldpos->z + $posdistance->z * $progress, $this->getLevel()), 2010));
		}
		return $safe; // Unnecessary but added just to stop PHPStorm from whining... And, Why not.
	}
}
