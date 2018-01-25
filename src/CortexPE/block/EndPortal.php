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

use CortexPE\Main;
use CortexPE\task\DelayedCrossDimensionTeleportTask;
use pocketmine\block\{
	Block, Solid
};
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\Player;
use pocketmine\Server;

class EndPortal extends Solid {

	/** @var int $id */
	protected $id = Block::END_PORTAL;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return int
	 */
	public function getLightLevel(): int{
		return 1;
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return "End Portal";
	}

	/**
	 * @return float
	 */
	public function getHardness(): float{
		return -1;
	}

	/**
	 * @return float
	 */
	public function getBlastResistance(): float{
		return 18000000;
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
	public function canPassThrough(): bool{
		return true;
	}

	/**
	 * @return bool
	 */
	public function hasEntityCollision(): bool{
		return true;
	}


	/**
	 * @param Entity $entity
	 *
	 */
	public function onEntityCollide(Entity $entity): void{
		if(Main::$registerDimensions){
			if($entity->getLevel()->getSafeSpawn()->distance($entity->asVector3()) <= 0.1){
				return;
			}
			if(!isset(Main::$onPortal[$entity->getId()])){
				Main::$onPortal[$entity->getId()] = true;
				if($entity instanceof Player){
					if($entity->getLevel() instanceof Level){
						if($entity->getLevel()->getName() != Main::$endName){ // OVERWORLD -> END
							Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask(Main::getInstance(), $entity, DimensionIds::THE_END, Main::$endLevel->getSafeSpawn()), 1);
						}else{ // END -> OVERWORLD
							Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask(Main::getInstance(), $entity, DimensionIds::OVERWORLD, Main::$overworldLevel->getSafeSpawn()), 1);
						}
					}
				}
				// TODO: Add mob teleportation
			}
		}

		return;
	}
}