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
use pocketmine\block\PoweredRail;
use pocketmine\block\Rail;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\level\particle\SmokeParticle;
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

	const TYPE_NORMAL = 1;
	const TYPE_CHEST = 2;
	const TYPE_HOPPER = 3;
	const TYPE_TNT = 4;
	const TYPE_FURNACE = 5;

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
	/** @var boolean */
	public $isInReverse = false;
	/** @var float */
	protected $baseOffset = 0.35;
	/** @var Block */
	public $displayBlock = null;
	/** @var bool */
	public $needUpdateBlock = false;
	/** @var float */
	private $currentSpeed = 0;

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
			$display = $this->displayBlock == null ? 0
				: $this->displayBlock->getId()
				| $this->displayBlock->getDamage() << 16;
			if($display == 0){
				$this->propertyManager->setByte(self::DATA_MINECART_HAS_DISPLAY, 0);
				$this->canInteract = $this->getType() === self::TYPE_NORMAL;

				return;
			}
			$this->propertyManager->setByte(self::DATA_MINECART_HAS_DISPLAY, 1);
			$this->propertyManager->setInt(self::DATA_MINECART_DISPLAY_BLOCK, $display);
			$this->propertyManager->setInt(self::DATA_MINECART_DISPLAY_OFFSET, 6);
		}

		if($display !== 0){
			$id = $display & 0xfff;
			$meta = $display >> 16;
			$this->displayBlock = Block::get($id, $meta);
		}

		$this->canInteract = $this->getType() === self::TYPE_NORMAL && $this->displayBlock === null;
	}

	public function saveNBT(): void{
		$this->saveEntityData();

		parent::saveNBT();
	}

	private function saveEntityData(){
		$hasDisplay = $this->propertyManager->getByte(self::DATA_MINECART_HAS_DISPLAY) == 1 || $this->displayBlock != null;
		$this->namedtag->setByte("CustomDisplayTile", $hasDisplay ? 1 : 0);
		if($hasDisplay){
			$display = $this->displayBlock->getId() | $this->displayBlock->getDamage() << 16;
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

	public function close(): void{
		parent::close();

		if($this->linkedEntity instanceof Player){
			/** @noinspection PhpUndefinedFieldInspection */
			$this->linkedEntity->riding = null;
			$this->linkedEntity = null;
		}

		if(!is_null($this->level)){
			$particle = new SmokeParticle($this);
			$this->level->addParticle($particle);
		}
	}

	public function getType(): int{
		return self::TYPE_NORMAL;
	}

	public function onUpdate(int $currentTick): bool{
		parent::onUpdate($currentTick);

		if($this->closed || !$this->isAlive()){
			return false;
		}

		$this->timings->startTiming();

		// Check if the block need to be updated (API)
		if($this->needUpdateBlock){
			$display = $this->displayBlock !== null ? ($this->displayBlock->getId() | ($this->displayBlock->getDamage()) << 16) : null;
			$this->propertyManager->setByte(self::DATA_MINECART_HAS_DISPLAY, $display === null ? 0 : 1);
			$this->propertyManager->setInt(self::DATA_MINECART_DISPLAY_BLOCK, $display === null ? 0 : $display);
			$this->propertyManager->setInt(self::DATA_MINECART_DISPLAY_OFFSET, 6);

			if($display !== null){
				$id = $display & 0xfff;
				$meta = $display >> 16;
				$this->displayBlock = Block::get($id, $meta);
			}

			$this->canInteract = $this->getType() == self::TYPE_NORMAL && $this->displayBlock === null;
			$this->needUpdateBlock = false;
		}

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
			$this->moveAlongTrack($dx, $dy, $dz, $block);
		}else{
			$this->setFalling();
		}

		# Minecart head
		$this->pitch = 0;
		$diffX = $this->lastX - $this->x;
		$diffZ = $this->lastZ - $this->z;
		$yawToChange = $this->yaw;
		if($diffX * $diffX + $diffZ * $diffZ > 0.001){
			$yawToChange = (atan2($diffZ, $diffX) * 180 / M_PI);
			if($this->isInReverse){
				$yawToChange += 180.0;
			}
		}

		$double = Math::wrapDegrees($yawToChange - $this->lastYaw);
		if(($double < -170.0) || ($double >= 170.0)){
			$yawToChange += 180.0;
			$this->isInReverse = (!$this->isInReverse);
		}

		$this->setRotation($yawToChange, $this->pitch);

		// Collisions
		foreach($this->level->getNearbyEntities($this->boundingBox->expand(0.2, 0, 0.2), $this) as $entity){
			if($entity !== $this->linkedEntity && $entity instanceof Minecart){
				$entity->applyCollisions($this); # Collisions advance
			}
		}

		$this->timings->stopTiming();

		return !$this->onGround or abs($this->motion->x) > 0.00001 or abs($this->motion->y) > 0.00001 or abs($this->motion->z) > 0.00001;
	}

	public function applyCollisions(Entity $entity){
		if($entity !== $this->linkedEntity){
			$motiveX = $entity->x - $this->x;
			$motiveZ = $entity->z - $this->z;
			$square = $motiveX * $motiveX + $motiveZ * $motiveZ;
			if($square >= 9.999999747378752E-5){
				$square = sqrt($square);
				$motiveX /= $square;
				$motiveZ /= $square;
				$next = 1 / $square;
				if($next > 1){
					$next = 1;
				}
				$motiveX *= $next;
				$motiveZ *= $next;
				$motiveX *= 0.10000000149011612;
				$motiveZ *= 0.10000000149011612;
				$motiveX *= 0.5;
				$motiveZ *= 0.5;
				if($entity instanceof Minecart){
					$densityX = $entity->x - $this->x;
					$densityZ = $entity->z - $this->z;
					$vector = (new Vector3($densityX, 0, $densityZ))->normalize();
					$vec = (new Vector3(cos($this->yaw * 0.017453292), 0, sin($this->yaw * 0.017453292)))->normalize();
					$densityXZ = abs($vector->dot($vec));

					if($densityXZ < 0.800000011920929){
						return;
					}

					$motX = $entity->motion->x + $this->motion->x;
					$motZ = $entity->motion->z + $this->motion->z;

					if($entity->getType() == self::TYPE_FURNACE && $this->getType() !== self::TYPE_FURNACE){
						$this->motion->x *= 0.20000000298023224;
						$this->motion->z *= 0.20000000298023224;
						$this->motion->x += $entity->motion->x - $motiveX;
						$this->motion->z += $entity->motion->z - $motiveZ;
						$entity->motion->x *= 0.949999988079071;
						$entity->motion->z *= 0.949999988079071;
					}elseif($entity->getType() !== self::TYPE_FURNACE && $this->getType() == self::TYPE_FURNACE){
						$entity->motion->x *= 0.20000000298023224;
						$entity->motion->z *= 0.20000000298023224;
						$this->motion->x += $entity->motion->x + $motiveX;
						$this->motion->z += $entity->motion->z + $motiveZ;
						$this->motion->x *= 0.949999988079071;
						$this->motion->z *= 0.949999988079071;
					}else{
						$motX /= 2;
						$motZ /= 2;
						$this->motion->x *= 0.20000000298023224;
						$this->motion->z *= 0.20000000298023224;
						$this->motion->x += $motX - $motiveX;
						$this->motion->z += $motZ - $motiveZ;
						$entity->motion->x *= 0.20000000298023224;
						$entity->motion->z *= 0.20000000298023224;
						$entity->motion->x += $motX + $motiveX;
						$entity->motion->z += $motZ + $motiveZ;
					}
				}else{
					$this->motion->x -= $motiveX;
					$this->motion->z -= $motiveZ;
				}
				$this->updateMovement();
			}
		}
	}

	private function moveAlongTrack(int $dx, int $dy, int $dz, Rail $block){
		$this->fallDistance = 0;
		$ascendingVector3Bef = $this->getPosOffset($this->x, $this->y, $this->z);

		$this->y = $dy;
		$isPowered = false;
		$isSlowed = false;
		if($block instanceof PoweredRail){
			$isPowered = true; #Todo 34: Powered rail
			$isSlowed = !$isPowered;
		}

		switch($block->getDamage()){
			case Rail::ASCENDING_NORTH:
				$this->motion->z += 0.0078125;
				$this->y += 1;
				break;
			case Rail::ASCENDING_SOUTH:
				$this->motion->z -= 0.0078125;
				$this->y += 1;
				break;
			case Rail::ASCENDING_EAST:
				$this->motion->z -= 0.0078125;
				$this->y += 1;
				break;
			case Rail::ASCENDING_WEST:
				$this->motion->z += 0.0078125;
				$this->y += 1;
				break;
		}

		$facing = $this->matrix[$block->getDamage()];
		$facedX = ($facing[1][0] - $facing[0][0]);
		$facedZ = ($facing[1][2] - $facing[0][2]);
		$nextSpeed = sqrt($facedX * $facedX + $facedZ * $facedZ);
		$speed = $this->motion->z * $facedX + $this->motion->z * $facedZ;

		if($speed < 0){
			$facedX = -$facedX;
			$facedZ = -$facedZ;
		}

		$squareOfFame = sqrt($this->motion->z * $this->motion->z + $this->motion->z * $this->motion->z);

		if($squareOfFame > 2){
			$squareOfFame = 2;
		}

		$this->motion->z = $squareOfFame * $facedX / $nextSpeed;
		$this->motion->z = $squareOfFame * $facedZ / $nextSpeed;

		if($this->linkedEntity !== null && $this->linkedEntity instanceof Living){
			$expectedSpeed = $this->currentSpeed;
			if($expectedSpeed > 0){
				$playerYawNeg = -sin($this->linkedEntity->yaw * M_PI / 180.0);
				$playerYawPos = cos($this->linkedEntity->yaw * M_PI / 180.0);
				$speed = $this->motion->z * $this->motion->z + $this->motion->z * $this->motion->z;
				if($speed < 0.01){
					$this->motion->z += $playerYawNeg * 0.1;
					$this->motion->z += $playerYawPos * 0.1;

					$isSlowed = false;
				}
			}
		}

		if($isSlowed){
			$expectedSpeed = sqrt($this->motion->z * $this->motion->z + $this->motion->z * $this->motion->z);
			if($expectedSpeed < 0.03){
				$this->motion->z *= 0;
				$this->motion->y *= 0;
				$this->motion->z *= 0;
			}else{
				$this->motion->z *= 0.5;
				$this->motion->y *= 0;
				$this->motion->z *= 0.5;
			}
		}

		$motionXT = $dx + 0.5 + $facing[0][0] * 0.5;
		$motionZT = $dz + 0.5 + $facing[0][2] * 0.5;
		$motionX = $dx + 0.5 + $facing[1][0] * 0.5;
		$motionZ = $dz + 0.5 + $facing[1][2] * 0.5;

		$facing1 = $motionX - $motionXT;
		$facing2 = $motionZ - $motionZT;

		if($facing1 == 0){
			$this->x = $dx + 0.5;
			$expectedSpeed = $this->z - $dz;
		}elseif($facing2 == 0){
			$this->z = $dz + 0.5;
			$expectedSpeed = $this->x - $dx;
		}else{
			$motX = $this->x - $motionXT;
			$motZ = $this->z - $motionZT;
			$expectedSpeed = ($motX * $facing1 + $motZ * $facing2) * 2;
		}

		$this->x = $motionXT + $facing1 * $expectedSpeed;
		$this->z = $motionZT + $facing2 * $expectedSpeed;
		$this->setPosition(new Vector3($this->x, $this->y, $this->z));

		$motX = $this->motion->z;
		$motZ = $this->motion->z;
		if($this->linkedEntity !== null){
			$motX *= 0.75;
			$motZ *= 0.75;
		}

		$motX = Math::clamp($motX, -0.4, 0.4);
		$motZ = Math::clamp($motZ, -0.4, 0.4);

		$this->move($motX, 0, $motZ);
		if($facing[0][1] !== 0 && \pocketmine\math\Math::floorFloat($this->x) - $dx === $facing[0][0] && \pocketmine\math\Math::floorFloat($this->z) - $dz === $facing[0][2]){
			$this->setPosition(new Vector3($this->x, $this->y + $facing[0][1], $this->z));
		}elseif($facing[1][1] !== 0 && \pocketmine\math\Math::floorFloat($this->x) - $dx === $facing[1][0] && \pocketmine\math\Math::floorFloat($this->z) - $dz === $facing[1][2]){
			$this->setPosition(new Vector3($this->x, $this->y + $facing[1][1], $this->z));
		}

		$this->applyDrag();
		$ascendingVector3Aft = $this->getPosOffset($this->x, $this->y, $this->z);

		if(!is_null($ascendingVector3Aft) && !is_null($ascendingVector3Bef)){
			$d14 = ($ascendingVector3Bef->y - $ascendingVector3Aft->y) * 0.05;

			$squareOfFame = sqrt($this->motion->z * $this->motion->z + $this->motion->z * $this->motion->z);
			if($squareOfFame > 0){
				$this->motion->z = $this->motion->z / $squareOfFame * ($squareOfFame + $d14);
				$this->motion->z = $this->motion->z / $squareOfFame * ($squareOfFame + $d14);
			}

			$this->setPosition(new Vector3($this->x, $ascendingVector3Aft->y, $this->z));
		}

		$floorX = \pocketmine\math\Math::floorFloat($this->x);
		$floorZ = \pocketmine\math\Math::floorFloat($this->z);

		if($floorX !== $dx || $floorZ !== $dz){
			$squareOfFame = sqrt($this->motion->z * $this->motion->z + $this->motion->z * $this->motion->z);
			$this->motion->z = $squareOfFame * ($floorX - $dx);
			$this->motion->z = $squareOfFame * ($floorZ - $dz);
		}

		if($isPowered){
			$blocksAfter = sqrt($this->motion->z * $this->motion->z + $this->motion->z * $this->motion->z);

			if($blocksAfter > 0.01){
				$blocksToGo = 0.06;

				$this->motion->z += $this->motion->z / $blocksAfter * $blocksToGo;
				$this->motion->z += $this->motion->z / $blocksAfter * $blocksToGo;
			}elseif($block->getDamage() === Rail::STRAIGHT_NORTH_SOUTH){
				if($this->isNormalBlock($this->level->getBlock(new Vector3($dx - 1, $dy, $dz)))){
					$this->motion->z = 0.02;
				}elseif($this->isNormalBlock($this->level->getBlock(new Vector3($dx + 1, $dy, $dz)))){
					$this->motion->z = -0.02;
				}
			}elseif($block->getDamage() === Rail::STRAIGHT_EAST_WEST){
				if($this->isNormalBlock($this->level->getBlock(new Vector3($dx, $dy, $dz - 1)))){
					$this->motion->z = 0.02;
				}elseif($this->isNormalBlock($this->level->getBlock(new Vector3($dx, $dy, $dz + 1)))){
					$this->motion->z = -0.02;
				}
			}
		}
	}

	private function applyDrag(){
		if($this->linkedEntity !== null){
			$this->motion->x *= 0.996999979019165;
			$this->motion->y *= 0.0;
			$this->motion->z *= 0.996999979019165;
		}else{
			$this->motion->x *= 0.9599999785423279;
			$this->motion->y *= 0.0;
			$this->motion->z *= 0.9599999785423279;
		}
	}

	private function setFalling(){
		$this->motion->x = Math::clamp($this->motion->x, -0.4, 0.4);
		$this->motion->z = Math::clamp($this->motion->z, -0.4, 0.4);

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

	public function onInteract(Player $player, Item $item, int $slot, Vector3 $clickPos): bool{
		if($this->linkedEntity != null){
			return false;
		}

		// Simple
		return parent::mountEntity($player);
	}

	private function getPosOffset($dx, $dy, $dz): ?Vector3{
		$checkX = (int)$dx;
		$checkY = (int)$dy;
		$checkZ = (int)$dz;

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

	public function isNormalBlock(Block $block): bool{
		return $block->isSolid() && !$block->isTransparent();
	}

	public function getDisplayOffset(): int{
		return $this->propertyManager->hasProperty(self::DATA_MINECART_DISPLAY_OFFSET) ? 0 : $this->propertyManager->getInt(self::DATA_MINECART_DISPLAY_OFFSET);
	}

	/**
	 * Set the block inside the minecart.
	 *
	 * @param $block Block type (can be null)
	 */
	public function setBlockTo(Block $block){
		$this->displayBlock = $block;
		$this->needUpdateBlock = true;
	}

	/**
	 * Get the block inside the minecart
	 *
	 * @return Block
	 */
	public function getBlock(): Block{
		return $this->displayBlock;
	}

	/**
	 * Unknown
	 *
	 * @param int $offset
	 * @return bool
	 */
	public function setOffset(int $offset): bool{
		if($this->displayBlock !== null){
			$this->propertyManager->setInt(self::DATA_MINECART_DISPLAY_OFFSET, $offset);

			return true;
		}

		return false;
	}
}