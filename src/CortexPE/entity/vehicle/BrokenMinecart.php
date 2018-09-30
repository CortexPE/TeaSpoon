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
 * @author larryTheCoder
 * @link https://CortexPE.xyz
 *
 */

namespace CortexPE\entity\vehicle;

use CortexPE\utils\Orientation;
use pocketmine\block\Block;
use pocketmine\block\Rail;
use pocketmine\item\Item;
use pocketmine\math\Math;
use pocketmine\math\Vector3;
use pocketmine\Player;


/**
 * A request for dummy and crappy minecart
 * from genisys by larryTheCoder.
 * Its truly CRAP than USUAL
 *
 * @package CortexPE\entity\vehicle
 */
class BrokenMinecart extends Vehicle {

	const NETWORK_ID = self::MINECART;

	const TYPE_NORMAL = 1;
	const TYPE_CHEST = 2;
	const TYPE_HOPPER = 3;
	const TYPE_TNT = 4;

	const STATE_INITIAL = 0;
	const STATE_ON_RAIL = 1;
	const STATE_OFF_RAIL = 2;

	public $height = 0.7;
	public $width = 0.98;
	public $drag = 0.1;
	public $gravity = 0.5;
	public $isMoving = false;
	public $moveSpeed = 0.4;

	private $state = BrokenMinecart::STATE_INITIAL;
	private $direction = -1;
	private $moveVector = [];

	public function initEntity(): void{
		$this->setMaxHealth(1);
		$this->setHealth($this->getMaxHealth());
		$this->moveVector[Vector3::SIDE_NORTH] = new Vector3(-1, 0, 0);
		$this->moveVector[Vector3::SIDE_SOUTH] = new Vector3(1, 0, 0);
		$this->moveVector[Vector3::SIDE_EAST] = new Vector3(0, 0, -1);
		$this->moveVector[Vector3::SIDE_WEST] = new Vector3(0, 0, 1);
		parent::initEntity();
	}

	public function getName(): string{
		return "Minecart";
	}

	public function getType(): int{
		return self::TYPE_NORMAL;
	}

	public function onInteract(Player $player, Item $item, int $slot, Vector3 $clickPos): bool{
		if($this->linkedEntity != null){
			return false;
		}

		// Simple
		return parent::mountEntity($player);
	}

	public function onUpdate($currentTick): bool{
		if($this->closed){
			return false;
		}

		$tickDiff = $currentTick - $this->lastUpdate;
		if($tickDiff <= 1){
			return false;
		}

		$this->lastUpdate = $currentTick;
		$this->timings->startTiming();

		parent::onUpdate($currentTick);

		if($this->isAlive()){
			$p = $this->getLinkedEntity();
			if($p instanceof Player){
				if($this->state === BrokenMinecart::STATE_INITIAL){
					$this->checkIfOnRail();
				}elseif($this->state === BrokenMinecart::STATE_ON_RAIL){
					$this->forwardOnRail($p);
					$this->updateMovement();
				}
			}
		}

		$this->timings->stopTiming();

		return true;
	}

	/**
	 * Check if minecart is currently on a rail and if so center the cart.
	 */
	private function checkIfOnRail(){
		for($y = -1; $y !== 2 and $this->state === BrokenMinecart::STATE_INITIAL; $y++){
			$positionToCheck = $this->temporalVector->setComponents($this->x, $this->y + $y, $this->z);
			$block = $this->level->getBlock($positionToCheck);
			if($this->isRail($block)){
				$minecartPosition = $positionToCheck->floor()->add(0.5, 0, 0.5);
				$this->setPosition($minecartPosition);    // Move minecart to center of rail
				$this->state = BrokenMinecart::STATE_ON_RAIL;
			}
		}
		if($this->state !== BrokenMinecart::STATE_ON_RAIL){
			$this->state = BrokenMinecart::STATE_OFF_RAIL;
		}
	}

	private function isRail(Block $rail){
		return ($rail !== null and in_array($rail->getId(), [Block::RAIL, Block::ACTIVATOR_RAIL, Block::DETECTOR_RAIL, Block::POWERED_RAIL]));
	}

	/**
	 * Attempt to move forward on rail given the direction the cart is already moving, or if not moving based
	 * on the direction the player is looking.
	 *
	 * @param Player $player Player riding the minecart.
	 *
	 * @return boolean True if minecart moved, false otherwise.
	 */
	private function forwardOnRail(Player $player){
		if($this->direction === -1){
			$candidateDirection = $player->getDirection();
		}else{
			$candidateDirection = $this->direction;
		}
		$rail = $this->getCurrentRail();
		if($rail !== null){
			$railType = $rail->getDamage();
			$nextDirection = $this->getDirectionToMove($railType, $candidateDirection);
			if($nextDirection !== -1){
				$this->direction = $nextDirection;
				$moved = $this->checkForVertical($railType, $nextDirection);
				if(!$moved){
					return $this->moveIfRail();
				}else{
					return true;
				}
			}else{
				$this->direction = -1;  // Was not able to determine direction to move, so wait for player to look in valid direction
			}
		}else{
			// Not able to find rail
			$this->state = BrokenMinecart::STATE_INITIAL;
		}

		return false;
	}

	private function getCurrentRail(){
		$block = $this->getLevel()->getBlock($this);
		if($this->isRail($block)){
			return $block;
		}
		// Rail could be one block below descending down
		$down = $this->temporalVector->setComponents($this->x, $this->y - 1, $this->z);
		$block = $this->getLevel()->getBlock($down);
		if($this->isRail($block)){
			return $block;
		}

		return null;
	}

	/**
	 * Determine the direction the minecart should move based on the candidate direction (current direction
	 * minecart is moving, or the direction the player is looking) and the type of rail that the minecart is on.
	 *
	 * @param int $railType Type of rail the minecart is on.
	 * @param int $candidateDirection Direction minecart already moving, or direction player looking.
	 *
	 * @return int The direction the minecart should move.
	 */
	private function getDirectionToMove($railType, $candidateDirection){
		switch($railType){
			case Rail::STRAIGHT_NORTH_SOUTH:
			case Orientation::ASCENDING_NORTH:
			case Orientation::ASCENDING_SOUTH:
				switch($candidateDirection){
					case Vector3::SIDE_NORTH:
					case Vector3::SIDE_SOUTH:
						return $candidateDirection;
				}
				break;
			case Orientation::STRAIGHT_EAST_WEST:
			case Orientation::ASCENDING_EAST:
			case Orientation::ASCENDING_WEST:
				switch($candidateDirection){
					case Vector3::SIDE_WEST:
					case Vector3::SIDE_EAST:
						return $candidateDirection;
				}
				break;
			case Orientation::CURVED_SOUTH_EAST:
				switch($candidateDirection){
					case Vector3::SIDE_SOUTH:
					case Vector3::SIDE_EAST:
						return $candidateDirection;
					case Vector3::SIDE_NORTH:
						return $this->checkForTurn($candidateDirection, Vector3::SIDE_EAST);
					case Vector3::SIDE_WEST:
						return $this->checkForTurn($candidateDirection, Vector3::SIDE_SOUTH);
				}
				break;
			case Orientation::CURVED_SOUTH_WEST:
				switch($candidateDirection){
					case Vector3::SIDE_SOUTH:
					case Vector3::SIDE_WEST:
						return $candidateDirection;
					case Vector3::SIDE_NORTH:
						return $this->checkForTurn($candidateDirection, Vector3::SIDE_WEST);
					case Vector3::SIDE_EAST:
						return $this->checkForTurn($candidateDirection, Vector3::SIDE_SOUTH);
				}
				break;
			case Orientation::CURVED_NORTH_WEST:
				switch($candidateDirection){
					case Vector3::SIDE_NORTH:
					case Vector3::SIDE_WEST:
						return $candidateDirection;
					case Vector3::SIDE_SOUTH:
						return $this->checkForTurn($candidateDirection, Vector3::SIDE_WEST);
					case Vector3::SIDE_EAST:
						return $this->checkForTurn($candidateDirection, Vector3::SIDE_NORTH);
				}
				break;
			case Orientation::CURVED_NORTH_EAST:
				switch($candidateDirection){
					case Vector3::SIDE_NORTH:
					case Vector3::SIDE_EAST:
						return $candidateDirection;
					case Vector3::SIDE_SOUTH:
						return $this->checkForTurn($candidateDirection, Vector3::SIDE_EAST);
					case Vector3::SIDE_WEST:
						return $this->checkForTurn($candidateDirection, Vector3::SIDE_NORTH);
				}
				break;
		}

		return -1;
	}

	/**
	 * Need to alter direction on curves halfway through the turn and reset the minecart to be in the middle of
	 * the rail again so as not to collide with nearby blocks.
	 *
	 * @param int $currentDirection Direction minecart currently moving
	 * @param int $newDirection Direction minecart should turn once has hit the halfway point.
	 *
	 * @return int Either the current direction or the new direction depending on haw far across the rail the minecart is.
	 */
	private function checkForTurn($currentDirection, $newDirection){
		switch($currentDirection){
			case Vector3::SIDE_NORTH:
				$diff = $this->x - $this->getFloorX();
				if($diff !== 0 and $diff <= .5){
					$dx = ($this->getFloorX() + .5) - $this->x;
					$this->move($dx, 0, 0);

					return $newDirection;
				}
				break;
			case Vector3::SIDE_SOUTH:
				$diff = $this->x - $this->getFloorX();
				if($diff !== 0 and $diff >= .5){
					$dx = ($this->getFloorX() + .5) - $this->x;
					$this->move($dx, 0, 0);

					return $newDirection;
				}
				break;
			case Vector3::SIDE_EAST:
				$diff = $this->z - $this->getFloorZ();
				if($diff !== 0 and $diff <= .5){
					$dz = ($this->getFloorZ() + .5) - $this->z;
					$this->move(0, 0, $dz);

					return $newDirection;
				}
				break;
			case Vector3::SIDE_WEST:
				$diff = $this->z - $this->getFloorZ();
				if($diff !== 0 and $diff >= .5){
					$dz = $dz = ($this->getFloorZ() + .5) - $this->z;
					$this->move(0, 0, $dz);

					return $newDirection;
				}
				break;
		}

		return $currentDirection;
	}

	private function checkForVertical($railType, $currentDirection){
		switch($railType){
			case Orientation::ASCENDING_NORTH:
				switch($currentDirection){
					case Vector3::SIDE_NORTH:
						// Headed north up
						$diff = $this->x - $this->getFloorX();
						if($diff !== 0 and $diff <= .5){
							$dx = ($this->getFloorX() - .1) - $this->x;
							$this->move($dx, 1, 0);

							return true;
						}
						break;
					case Vector3::SIDE_SOUTH:
						// Headed south down
						$diff = $this->x - $this->getFloorX();
						if($diff !== 0 and $diff >= .5){
							$dx = ($this->getFloorX() + 1) - $this->x;
							$this->move($dx, -1, 0);

							return true;
						}
						break;
				}
				break;
			case Orientation::ASCENDING_SOUTH:
				switch($currentDirection){
					case Vector3::SIDE_SOUTH:
						// Headed south up
						$diff = $this->x - $this->getFloorX();
						if($diff !== 0 and $diff >= .5){
							$dx = ($this->getFloorX() + 1) - $this->x;
							$this->move($dx, 1, 0);

							return true;
						}
						break;
					case Vector3::SIDE_NORTH:
						// Headed north down
						$diff = $this->x - $this->getFloorX();
						if($diff !== 0 and $diff <= .5){
							$dx = ($this->getFloorX() - .1) - $this->x;
							$this->move($dx, -1, 0);

							return true;
						}
						break;
				}
				break;
			case Orientation::ASCENDING_EAST:
				switch($currentDirection){
					case Vector3::SIDE_EAST:
						// Headed east up
						$diff = $this->z - $this->getFloorZ();
						if($diff !== 0 and $diff <= .5){
							$dz = ($this->getFloorZ() - .1) - $this->z;
							$this->move(0, 1, $dz);

							return true;
						}
						break;
					case Vector3::SIDE_WEST:
						// Headed west down
						$diff = $this->z - $this->getFloorZ();
						if($diff !== 0 and $diff >= .5){
							$dz = ($this->getFloorZ() + 1) - $this->z;
							$this->move(0, -1, $dz);

							return true;
						}
						break;
				}
				break;
			case Orientation::ASCENDING_WEST:
				switch($currentDirection){
					case Vector3::SIDE_WEST:
						// Headed west up
						$diff = $this->z - $this->getFloorZ();
						if($diff !== 0 and $diff >= .5){
							$dz = ($this->getFloorZ() + 1) - $this->z;
							$this->move(0, 1, $dz);

							return true;
						}
						break;
					case Vector3::SIDE_EAST:
						// Headed east down
						$diff = $this->z - $this->getFloorZ();
						if($diff !== 0 and $diff <= .5){
							$dz = ($this->getFloorZ() - .1) - $this->z;
							$this->move(0, -1, $dz);

							return true;
						}
						break;
				}
				break;
		}

		return false;
	}

	/**
	 * Move the minecart as long as it will still be moving on to another piece of rail.
	 *
	 * @return bool True if the minecart moved.
	 */
	private function moveIfRail(){
		$nextMoveVector = $this->moveVector[$this->direction];
		$nextMoveVector = $nextMoveVector->multiply($this->moveSpeed);
		$newVector = $this->add($nextMoveVector->x, $nextMoveVector->y, $nextMoveVector->z);
		$possibleRail = $this->getCurrentRail();
		if(in_array($possibleRail->getId(), [Block::RAIL, Block::ACTIVATOR_RAIL, Block::DETECTOR_RAIL, Block::POWERED_RAIL])){
			$this->moveUsingVector($newVector);

			return true;
		}

		return false;
	}

	/**
	 * Invoke the normal move code, but first need to convert the desired position vector into the
	 * delta values from the current position.
	 *
	 * @param Vector3 $desiredPosition
	 */
	private function moveUsingVector(Vector3 $desiredPosition){
		$dx = $desiredPosition->x - $this->x;
		$dy = $desiredPosition->y - $this->y;
		$dz = $desiredPosition->z - $this->z;
		$this->move($dx, $dy, $dz);
	}

	/**
	 * @return Rail
	 */
	public function getNearestRail(){
		$minX = Math::floorFloat($this->boundingBox->minX);
		$minY = Math::floorFloat($this->boundingBox->minY);
		$minZ = Math::floorFloat($this->boundingBox->minZ);
		$maxX = Math::ceilFloat($this->boundingBox->maxX);
		$maxY = Math::ceilFloat($this->boundingBox->maxY);
		$maxZ = Math::ceilFloat($this->boundingBox->maxZ);
		$rails = [];
		for($z = $minZ; $z <= $maxZ; ++$z){
			for($x = $minX; $x <= $maxX; ++$x){
				for($y = $minY; $y <= $maxY; ++$y){
					$block = $this->level->getBlock($this->temporalVector->setComponents($x, $y, $z));
					if(in_array($block->getId(), [Block::RAIL, Block::ACTIVATOR_RAIL, Block::DETECTOR_RAIL, Block::POWERED_RAIL])) $rails[] = $block;
				}
			}
		}
		$minDistance = PHP_INT_MAX;
		$nearestRail = null;
		foreach($rails as $rail){
			$dis = $this->distance($rail);
			if($dis < $minDistance){
				$nearestRail = $rail;
				$minDistance = $dis;
			}
		}

		return $nearestRail;
	}
}