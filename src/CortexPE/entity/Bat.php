<?php

/**
 *
 * MMP""MM""YMM               .M"""bgd
 * P'   MM   `7              ,MI    "Y
 *      MM  .gP"Ya   ,6"Yb.  `MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.
 *      MM ,M'   Yb 8)   MM    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM
 *      MM 8M""""""  ,pm9MM  .     `MM MM    M8 8M     M8 8M     M8 MM    MM
 *      MM YM.    , 8M   MM  Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM
 *    .JMML.`Mbmmd' `Moo9^Yo.P"Ybmmd"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML.
 *                                     MM
 *                                   .JMML.
 * This file is part of TeaSpoon.
 *
 * TeaSpoon is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeaSpoon is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TeaSpoon.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author CortexPE
 * @link http://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE\entity;

use pocketmine\entity\Animal;
use pocketmine\level\Level;
use pocketmine\nbt\tag\{ByteTag, CompoundTag};

class Bat extends Animal {
	const NETWORK_ID = self::BAT;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 0.6;

	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->isResting)){
			$nbt->isResting = new ByteTag("isResting", 0);
		}
		parent::__construct($level, $nbt);

		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_RESTING, $this->isResting());
	}

	public function isResting(): bool{
		return boolval($this->namedtag["isResting"]);
	}

	public function getName(): string{
		return "Bat";
	}

	public function initEntity(){
		$this->setMaxHealth(6);
		parent::initEntity();
	}

	public function setResting(bool $resting){
		$this->namedtag->isResting = new ByteTag("isResting", $resting ? 1 : 0);
	}

	public function onUpdate(int $currentTick): bool{
		if($this->age > 1200){
			// I have reached the lifespan of my species...
			// Existance is pain.
			// I JUST WANT TO DIE
			// ~ Mr. Meeseeks
			$this->kill();
		}

		return parent::onUpdate($currentTick);
	}
}
