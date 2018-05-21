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
 * @link https://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE\entity\object;

use CortexPE\Main;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Explosion;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;

class EndCrystal extends Entity {

	public const TAG_SHOW_BOTTOM = "ShowBottom";

	const NETWORK_ID = self::ENDER_CRYSTAL;
	public $height = 0.98;
	public $width = 0.98;

	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag(self::TAG_SHOW_BOTTOM, ByteTag::class)){
			$nbt->setByte(self::TAG_SHOW_BOTTOM, 0);
		}
		parent::__construct($level, $nbt);

		// TODO: The data flag for showing bottom & beam? maybe... I still haven't decompiled the MCPE Source code... takes a long time.
	}

	public function isShowingBottom(): bool{
		return boolval($this->namedtag->getByte(self::TAG_SHOW_BOTTOM));
	}

	public function setShowingBottom(bool $value){
		$this->namedtag->setByte(self::TAG_SHOW_BOTTOM, intval($value));
	}

	public function setBeamTarget(Vector3 $pos){
		$this->namedtag->setTag(new ListTag("BeamTarget", [
			new DoubleTag("", $pos->getX()),
			new DoubleTag("", $pos->getY()),
			new DoubleTag("", $pos->getZ()),
		]));
	}

	public function attack(EntityDamageEvent $source): void{
		if(Main::$endCrystalExplode){
			if($this->isClosed()){
				return;
			}
			$pos = clone $this->asPosition();
			$this->close();
			$explode = new Explosion($pos, Main::$endCrystalPower, $this);
			$explode->explodeA();
			$explode->explodeB();
		}
	}
}
