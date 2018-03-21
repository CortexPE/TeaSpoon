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

namespace CortexPE\block;

use CortexPE\{
	Main, Utils
};
use pocketmine\block\{
	Block, BlockFactory, Fire as PMFire
};
use pocketmine\math\Vector3;

class Fire extends PMFire {

	public function onScheduledUpdate(): void{
		if($this->meta >= 15){
			$this->level->setBlock($this, BlockFactory::get(Block::AIR));
		}else{
			$this->meta += mt_rand(1, 4);
			$this->level->setBlock($this, $this);
		}
	}

	public function onRandomTick(): void{
		$weather = Main::$weatherData[$this->getLevel()->getId()];
		$forever = ($this->getSide(Vector3::SIDE_DOWN)->getId() == Block::NETHERRACK);
		if(!$forever){
			if($weather->canCalculate()){
				$rainy = ($weather->isRainy() || $weather->isRainyThunder());

				if($rainy &&
					(
						Utils::canSeeSky($this->getLevel(), $this->asVector3()) ||
						Utils::canSeeSky($this->getLevel(), $this->getSide(Vector3::SIDE_NORTH)) ||
						Utils::canSeeSky($this->getLevel(), $this->getSide(Vector3::SIDE_SOUTH)) ||
						Utils::canSeeSky($this->getLevel(), $this->getSide(Vector3::SIDE_EAST)) ||
						Utils::canSeeSky($this->getLevel(), $this->getSide(Vector3::SIDE_WEST))
					)
				){
					$this->level->setBlock($this, BlockFactory::get(Block::AIR));
				}
			}
		}
	}
}
