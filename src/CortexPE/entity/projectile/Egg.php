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

namespace CortexPE\entity\projectile;

use pocketmine\block\Block;
use pocketmine\entity\{
	Entity, projectile\Throwable
};
use pocketmine\level\particle\DestroyBlockParticle;

class Egg extends Throwable {
	const NETWORK_ID = self::EGG;

	const RAND_POS_X = [-0.5, 0, 0.5];
	const RAND_POS_Y = [0, 1];
	const RAND_POS_Z = [-0.5, 0, 0.5];
	const RAND_SCALE = [0.75, 1];

	public function onUpdate(int $currentTick): bool{
		if($this->isCollided || $this->age > 1200){
			if(mt_rand(1, 8) == 4){
				$nbt = Entity::createBaseNBT($this);
				$chick = Entity::createEntity("Chicken", $this->getLevel(), $nbt);
				$chick->setScale(self::RAND_SCALE[array_rand(self::RAND_SCALE)]);
				$chick->spawnToAll();
			}elseif(mt_rand(1, 32) == 16){
				for($c = 1; $c <= 4; $c++){
					$randomX = self::RAND_POS_X[array_rand(self::RAND_POS_X)];
					$randomY = self::RAND_POS_Y[array_rand(self::RAND_POS_Y)];
					$randomZ = self::RAND_POS_Z[array_rand(self::RAND_POS_Z)];

					$nbt = Entity::createBaseNBT($this->add($randomX, $randomY, $randomZ));
					$chick = Entity::createEntity("Chicken", $this->getLevel(), $nbt);
					$chick->setScale(0.75); // A bit hacky. but just wait till PMMP adds proper Mob Aging.
					$chick->spawnToAll();
				}
			}
			$this->getLevel()->addParticle(new DestroyBlockParticle($this, Block::get(Block::BROWN_MUSHROOM_BLOCK)));

			$this->close();
		}

		return parent::onUpdate($currentTick);
	}

	public function onCollideWithEntity(Entity $entity){
		return;
	}
}