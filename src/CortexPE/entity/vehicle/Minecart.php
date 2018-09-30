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
use CortexPE\utils\Math;
use CortexPE\utils\RailUtils;
use pocketmine\block\Block;
use pocketmine\block\Rail;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\event\TimingsHandler;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
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

	/** @var Block */
	public $blockInside = null;

	/** @var float */
	private $currentSpeed;

	public function initEntity(): void{
		parent::initEntity();

		// Now with the custom block data
		if($this->namedtag->hasTag("CustomDisplayTile", ByteTag::class)
			&& $this->namedtag->getByte("CustomDisplayTile") === 1){
			$display = $this->namedtag->getByte("DisplayTile");
			$offSet = $this->namedtag->getInt("DisplayOffset");
			$this->propertyManager->setByte(self::DATA_MINECART_HAS_DISPLAY, 1);
			$this->propertyManager->setInt(self::DATA_MINECART_DISPLAY_BLOCK, $display);
			$this->propertyManager->setInt(self::DATA_MINECART_DISPLAY_OFFSET, $offSet);
		}else{
			$display = $this->blockInside == null ? 0
				: $this->blockInside->getId()
				| $this->blockInside->getDamage() << 16;
			if($display == 0){
				$this->propertyManager->setByte(self::DATA_MINECART_HAS_DISPLAY, 0);

				return;
			}
			$this->propertyManager->setByte(self::DATA_MINECART_HAS_DISPLAY, 1);
			$this->propertyManager->setInt(self::DATA_MINECART_DISPLAY_BLOCK, $display);
			$this->propertyManager->setInt(self::DATA_MINECART_DISPLAY_OFFSET, 6);
		}

		//var_dump($this->getId());
	}

	public function saveNBT(): void{
		$this->saveEntityData();

		parent::saveNBT();
	}

	private function saveEntityData(){
		$hasDisplay = $this->propertyManager->getByte(self::DATA_MINECART_HAS_DISPLAY) == 1 || $this->blockInside != null;
		$this->namedtag->setByte("CustomDisplayTile", $hasDisplay ? 1 : 0);
		if($hasDisplay){
			$display = $this->blockInside->getId() | $this->blockInside->getDamage() << 16;
			$offSet = $this->propertyManager->getInt(self::DATA_MINECART_DISPLAY_OFFSET);
			$this->namedtag->setInt("DisplayTile", $display);
			$this->namedtag->setInt("DisplayOffset", $offSet);
		}
	}

	public function getDrops(): array{
		return [
			Item::get(Item::MINECART, 0, 1),
		];
	}

	public function onUpdate(int $currentTick): bool{
		//$this->printTimings();
		if($this->closed){
			return false;
		}

		if(!$this->isAlive()){
			$this->despawnFromAll();
			$this->close();

			return true;
		}

		$tickDiff = $currentTick - $this->lastUpdate;

		if($tickDiff <= 0){
			return false;
		}

		$this->timings->startTiming();

		$this->lastUpdate = $currentTick;

		if($this->isAlive()){
			parent::onUpdate($currentTick);

			$this->motion->y -= 0.03999999910593033;
			$dx = $this->getFloorX();
			$dy = $this->getFloorY();
			$dz = $this->getFloorZ();

			if(RailUtils::isRailBlock($this->level->getBlockIdAt($dx, $dy - 1, $dz))){
				--$dy;
			}

			$block = $this->level->getBlock(new Vector3($dx, $dy, $dz));

			if(RailUtils::isRailBlock($block)){
				/** @var $block Rail */
				$this->processMovement($dx, $dy, $dz, $block);
			}else{
				// THIS IS USING 50% CPU USAGE
				$this->setFalling();
			}

			$pitch = 0;
			$diffX = $this->lastX - $this->x;
			$diffZ = $this->lastZ - $this->z;
			$yawToChange = $this->yaw;
			if($diffX * $diffX + $diffZ * $diffZ > 0.001){
				$yawToChange = (atan2($diffZ, $diffX) * 180 / M_PI);
			}

			// Reverse yaw if yaw is below 0
			if($yawToChange < 0){
				// -90-(-90)-(-90) = 90
				$yawToChange = -$yawToChange;
			}

			$this->setRotation($yawToChange, $pitch);

			// Collisions
			foreach($this->level->getNearbyEntities($this->boundingBox->expand(0.2, 0, 0.2), $this) as $entity){
				// Okay a player is having collision with the minecart
				if($entity instanceof Living){
					$this->applyEntityCollision($entity);
				}
				// Hmm, the minecart colliding each other like a mohron
				if($entity !== $this->linkedEntity && $entity instanceof Minecart){
					$this->applyEntityCollision($this);
				}
			}
		}
		$this->timings->stopTiming();

		return !$this->onGround or abs($this->motion->x) > 0.00001 or abs($this->motion->y) > 0.00001 or abs($this->motion->z) > 0.00001;
	}

	public function applyEntityCollision(Entity $entity){
		if($entity !== $this->linkedEntity){
			if($entity instanceof Living
				&& !($entity instanceof Human)
				&& $this->motion->x ^ 2 + $this->motion->z ^ 2 > 0.1
				&& $this->linkedEntity === null
				&& (!isset($entity->riding) || $entity->riding === null)){
				// This is the place to keep shits inside the minecart
				// Beware of bloatware-entities
			}

			$radX = $entity->x - $this->x;
			$radZ = $entity->z - $this->z;
			$distance = $radX ^ 2 + $radZ ^ 2;

			if($distance >= 9.999999747378752E-5){
				$distance = sqrt($distance);
				$radX /= $distance;
				$radZ /= $distance;
				$limit = 1 / $distance;

				if($limit > 1){
					$limit = 1;
				}

				$radX *= $limit;
				$radZ *= $limit;
				$radX *= 0.6;
				$radZ *= 0.6;

				if($entity instanceof Minecart){
					$distX = $entity->x - $this->x;
					$distZ = $entity->z - $this->z;

					$vector = new Vector3($distX, 0, $distZ);
					$vector = $vector->normalize();
					// Note to self: cos = Adjacent/Opposite which it should be (cos = x, tan = y, sin = z)
					// xyz = cartesian table
					$vec = new Vector3(cos($this->yaw * 0.017453292), 0, sin($this->yaw * 0.017453292));
					$vec = $vec->normalize();
					$distXZ = abs($vector->dot($vec));

					if($distXZ < 0.8){
						return;
					}

					$motX = $entity->motion->x + $this->motion->x;
					$motZ = $entity->motion->z + $this->motion->z;

					// TODO: Furnaces has their own weight to reproduce its collision, implement it
					$motX /= 2;
					$motZ /= 2;
					$this->motion->x *= 0.20000000298023224;
					$this->motion->z *= 0.20000000298023224;
					$this->motion->x += $motX - $radX;
					$this->motion->z += $motZ - $radZ;
					$entity->motion->x *= 0.2;
					$entity->motion->z *= 0.2;
					$entity->motion->x += $motX + $radX;
					$entity->motion->z += $motZ + $radZ;
				}else{
					$this->motion->x -= $radX;
					$this->motion->z -= $radZ;
				}
				//var_dump($this->motion);
			}
		}
	}

	private function processMovement(int $dx, int $dy, int $dz, Rail $block){

	}

	private $ticks = 0;

	private function setFalling(){
		// Not sure what is causing the performance issue on this
		// But ticking this slowly will fix it
		if($this->ticks >= 6){
			$motionX = $this->motion->x;
			$motionY = $this->motion->y;
			$motionZ = $this->motion->z;

			$motionX = Math::clamp($motionX, -0.4, 0.4);
			$motionZ = Math::clamp($motionZ, -0.4, 0.4);

			if($this->onGround){
				$motionX *= 0.5;
				$motionY *= 0.5;
				$motionZ *= 0.5;
			}

			$this->move($motionX, $motionY, $motionZ);
			if(!$this->onGround){
				$motionX *= 0.95;
				$motionY *= 0.95;
				$motionZ *= 0.95;
			}

			$this->setMotion(new Vector3($motionX, $motionY, $motionZ));
			$this->ticks = 0;
		}else{
			$this->ticks++;
		}
	}

	/**
	 * Used to multiply the minecart current speed
	 *
	 * @param $speed float The speed of the minecart that will be calculated
	 */
	public function setCurrentSpeed(float $speed){
		$this->currentSpeed = $speed;
	}

	public function onInteract(Player $player, Item $item, int $slot, Vector3 $clickPos): bool{
		if($this->linkedEntity != null){
			return false;
		}

		// Simple
		return parent::mountEntity($player);
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
			$facing = $this->matrix[$block->getVariant()];
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

	private function printTimings(){
		$resource = fopen(Main::getInstance()->getDataFolder() . "timings-{$this->ticksLived}.txt", "w");
		TimingsHandler::printTimings($resource);
		fclose($resource);
	}
}