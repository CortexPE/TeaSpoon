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

namespace CortexPE\entity;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;

class EndCrystal extends Entity {
	const NETWORK_ID = self::ENDER_CRYSTAL;

	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->ShowBottom)){
			$nbt->ShowBottom = new ByteTag("ShowBottom", 0);
		}
		parent::__construct($level, $nbt);

		// TODO: The data flag for showing bottom & beam? maybe... I still haven't decompiled the MCPE Source code... takes a long time.
	}

	public function isShowingBottom(): bool{
		return boolval($this->namedtag["ShowBottom"]);
	}

	public function setShowingBottom(bool $value){
		$this->namedtag->ShowBottom = new ByteTag("ShowBottom", $value ? 1 : 0);
	}

	public function setBeamTarget(Vector3 $pos){
		$this->namedtag->BeamTarget = new ListTag("BeamTarget", [
			new DoubleTag("", $pos->getX()),
			new DoubleTag("", $pos->getY()),
			new DoubleTag("", $pos->getZ()),
		]);
	}

	// explosions are handled via EventListener...
}