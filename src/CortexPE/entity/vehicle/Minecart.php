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

namespace CortexPE\entity\vehicle;

use CortexPE\Main;
use CortexPE\utils\RailUtils;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\Vehicle;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

/**
 * The main class for the entity minecart.
 *
 * @author larryTheCoder
 * @author CortexPE
 */
class Minecart extends Vehicle {

	public const NETWORK_ID = self::MINECART;

	private $matrix = [
		[[0, 0, -1], [0, 0, 1]],
		[[-1, 0, 0], [1, 0, 0]],
		[[-1, -1, 0], [1, 0, 0]],
		[[-1, 0, 0], [1, -1, 0]],
		[[0, 0, -1], [0, -1, 1]],
		[[0, -1, -1], [0, 0, 1]],
		[[0, 0, 1], [1, 0, 0]],
		[[0, 0, 1], [-1, 0, 0]],
		[[0, 0, -1], [-1, 0, 0]],
		[[0, 0, -1], [1, 0, 0]],
	];

	public $height = 0.8;
	public $width = 0.98;
	public $gravity = 1.5; // idk but I'm pretty sure this isnt like this in vanilla. Minecarts are just cars for now anyways xD
	public $drag = 0.1;

	/** @var Living */
	public $rider = null;

	/** @var Entity */
	public $linkedEntity = null;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
	}

	public function initEntity(): void{
		parent::initEntity();
		$this->setHealth(2);
		$this->setMaxHealth(2);
	}

	public function getDrops(): array{
		return [
			Item::get(Item::MINECART, 0, 1),
		];
	}

	public function onUpdate(int $currentTick): bool{
		$parent = parent::onUpdate($currentTick);
		if($this->rider !== null){
			// Ooof, seriously cortex?
			$mot = $this->rider->getDirectionVector()->multiply(2);
			$mot->y = -$this->gravity;
			//$mot->y = 0;
			$this->teleport($this->rider);
			$this->rider->setMotion($mot);
		}

		return $parent;
	}

	private function getNextRail($dx, $dy, $dz): Vector3{
		$checkX = $dx;
		$checkY = $dy;
		$checkZ = $dz;

		if(RailUtils::isRailBlock($this->level->getBlockIdAt($checkX, $checkY - 1, $checkZ))){
			--$checkY;
		}

		$block = $this->level->getBlock(new Vector3($checkX, $checkY, $checkZ));

		if(RailUtils::isRailBlock($block)){
			$facing = $this->matrix[$block->getDamage()];
			// Genisys mistake (Doesn't check surrounding more exactly)
			$nextOne = $checkX + 0.5 + $facing[0][0] * 0.5;
			$nextTwo = $checkY + 0.5 + $facing[0][1] * 0.5;
			$nextThree = $checkZ + 0.5 + $facing[0][2] * 0.5;
			$nextFour = $checkX + 0.5 + $facing[1][0] * 0.5;
			$nextFive = $checkY + 0.5 + $facing[1][1] * 0.5;
			$nextSix = $checkZ + 0.5 + $facing[1][2] * 0.5;
			$nextSeven = $nextFour - $nextOne;
			$nextEight = ($nextFive - $nextTwo) * 2;
			$nextMax = $nextSix - $nextThree;

			if($nextSeven == 0){
				$rail = $dz - $checkZ;
			}elseif($nextMax == 0){
				$rail = $dx - $checkX;
			}else{
				$whatOne = $dx - $nextOne;
				$whatTwo = $dz - $nextThree;

				$rail = ($whatOne * $nextSeven + $whatTwo * $nextMax) * 2;
			}

			$dx = $nextOne + $nextSeven * $rail;
			$dy = $nextTwo + $nextEight * $rail;
			$dz = $nextThree + $nextMax * $rail;
			if($nextEight < 0){
				++$dy;
			}

			if($nextEight > 0){
				$dy += 0.5;
			}

			return new Vector3($dx, $dy, $dz);
		}else{
			return null;
		}
	}

	public function onInteract(Player $player, Item $item, int $slot, Vector3 $clickPos): bool{
		$this->rider = $player;
		Main::getInstance()->getSessionById($player->getId())->vehicle = $this;

		/*$pk = new SetEntityLinkPacket();
		$link = new EntityLink($this->getId(), $player->getId(), 2, true); // todo: figure out what that last boolean is
		$pk->link = $link;
		$player->getServer()->broadcastPacket($this->getViewers(), $pk);
		$this->rider->getDataPropertyManager()->setVector3(Entity::DATA_RIDER_SEAT_POSITION, new Vector3(0, 0, 0));*/

		return true;
	}
}
