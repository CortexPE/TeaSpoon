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

use CortexPE\utils\Math;
use CortexPE\utils\RailUtils;
use pocketmine\block\Block;
use pocketmine\block\Rail;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
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

	/** @var Block */
	public $blockInside = null;

	/** @var float */
	private $currentSpeed;

	public function initEntity(): void{
		parent::initEntity();

		$this->setRollingAmplitude(0);
		$this->setRollingDirection(1);
		$this->setDamage(0);

		// Now with the custom block data
		if(!is_null($this->namedtag->getByte("CustomDisplayTile"))){
			$display = $this->namedtag->getInt("DisplayTile");
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

	public function attack(EntityDamageEvent $source): void{
		$damage = null;
		$instantKill = false;
		if($source instanceof EntityDamageByEntityEvent){
			$damage = $source->getDamager();
			$instantKill = $damage instanceof Player && $damage->isCreative();
		}

		if(!$instantKill) $this->performHurtAnimation($source->getFinalDamage());

		if($instantKill || $this->getDamage() > 40){
			if($this->linkedEntity != null){
				$this->mountEntity($this->linkedEntity);
			}

			if($instantKill){
				$this->kill();
			}else{
				$this->close();
			}
		}
	}

	public function onUpdate(int $currentTick): bool{
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

		$this->lastUpdate = $currentTick;

		if($this->isAlive()){
			parent::onUpdate($currentTick);

			$this->motion->y -= 0.03999999910593033;
			$dx = $this->x;
			$dy = $this->y;
			$dz = $this->z;

			if(RailUtils::isRailBlock($this->level->getBlockIdAt($dx, $dy - 1, $dz))){
				--$dy;
			}

			$block = $this->level->getBlock(new Vector3($dx, $dy, $dz));

			if(RailUtils::isRailBlock($block)){
				/** @var $block Rail */
				$this->processMovement($dx, $dy, $dz, $block);
			}else{
				$this->setFalling();
			}

			$this->checkBlockCollision();

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
			foreach($this->level->getNearbyEntities($this->boundingBox->grow(0.2, 0, 0.2), $this) as $entity){
				if($entity->getId() != $this->linkedEntity->getId() && $entity instanceof Minecart){
					// TODO: Add this crappy dang thingy
					//$entity->applyEntityCollision($this);
				}
			}

		}

		return true;
	}

	private function processMovement(int $dx, int $dy, int $dz, Rail $block){

	}

	private $hasUpdated = false;

	private function setFalling(){
		$this->motion->x = Math::clamp($this->motion->x, -0.4, 0.4);
		$this->motion->z = Math::clamp($this->motion->z, -0.4, 0.4);

		if($this->linkedEntity != null && !$this->hasUpdated){
			$this->propertyManager->setVector3(self::DATA_RIDER_SEAT_POSITION, new Vector3(0, $this->baseOffset, 0));
			$this->hasUpdated = true;
		}else{
			$this->hasUpdated = false;
		}

		if($this->onGround){
			$this->motion->x *= 0.5;
			$this->motion->y *= 0.5;
			$this->motion->z *= 0.5;
		}

		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
		if(!$this->onGround){
			$this->motion->x *= 0.95;
			$this->motion->y *= 0.95;
			$this->motion->z *= 0.95;
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

	public function onInteract(Player $player, Item $item, int $slot, Vector3 $clickPos): bool{
		if($this->linkedEntity != null){
			return false;
		}

		// Simple
		return parent::mountEntity($player);
	}
}