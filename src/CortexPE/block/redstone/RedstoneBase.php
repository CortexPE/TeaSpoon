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

namespace CortexPE\block\redstone;


use pocketmine\block\Block;
use pocketmine\math\Vector3;

class RedstoneBase extends Block {
	/** @var int */
	protected $power = 0;
	/** @var Vector3 */
	protected $sourcePos = null; // Is this hacky? idk. I dont think this is up to par xD

	public function isActivated(): bool{
		return ($this->power > 0);
	}

	public function setActive(int $power, Vector3 $sourcePosition): void{
		$this->setPower($power);
		$this->setSource($sourcePosition);
	}

	public function setPower(int $power): void{
		if($power < 0 || $power > 15){
			throw new \InvalidArgumentException("Power must be within the range of 0-15");
		}

		$this->power = $power;
	}

	public function getPower(): int{
		return $this->power;
	}

	public function onNearbyBlockChange(): void{
		parent::onNearbyBlockChange();
		$this->updateMeta();
	}

	public function updateMeta(): void{

	}

	public function setSource(Vector3 $pos): void{
		$this->sourcePos = $pos;
	}

	public function getSource(): Vector3{
		if(!($this->sourcePos instanceof Vector3)){
			throw new \RuntimeException("Redstone Source Position is invalid.");
		}

		return $this->sourcePos;
	}
}