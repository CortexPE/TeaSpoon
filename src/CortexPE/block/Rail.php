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

use CortexPE\utils\Orientation;
use pocketmine\block\Block;
use pocketmine\block\Rail as PMRail;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

/**
 * One stop wiki-page: http://minecraft.gamepedia.com/Rail
 * This is the class where the block being checking to
 * intersect with the other rail. This follows the minecraft
 * vanilla rails.
 *
 * @author larryTheCoder
 * @package CortexPE\block
 */
class Rail extends PMRail {

	// Rail curves and orientation
	const STRAIGHT_NORTH_SOUTH = 0;
	const STRAIGHT_EAST_WEST = 1;
	const ASCENDING_EAST = 2;
	const ASCENDING_WEST = 3;
	const ASCENDING_NORTH = 4;
	const ASCENDING_SOUTH = 5;
	const CURVED_SOUTH_EAST = 6;
	const CURVED_SOUTH_WEST = 7;
	const CURVED_NORTH_WEST = 8;
	const CURVED_NORTH_EAST = 9;

	/** @var Orientation[] */
	public static $railMetadata;

	protected $id = self::RAIL;

	protected $canBePowered = false;

	public function __construct(int $meta = 0){
		parent::__construct($meta);

		self::$railMetadata = Orientation::getMetadata();
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
		$down = $this->getSide(Vector3::SIDE_DOWN);
		if(is_null($down) || $down->isTransparent()){
			return false;
		}

		// Horizontal rules
		$railsAround = $this->checkRailsAroundAffected();
		$railSides = count($railsAround);

		/** @var int[] $sides */
		$sides = array_keys($railsAround);
		/** @var Rail[] $rails */
		$rails = array_values($railsAround);

		if($railSides === 1){
			// only 1 sides
			$damage = $this->connectTo($rails[0], $sides[0]);
			$this->setDamage($damage->getDamage());
		}elseif($railSides === 4){
			// Multiple intersect 4-rails within railway
			if($this->canBeCurved()){
				$railSouth = $railsAround[Vector3::SIDE_SOUTH];
				$railEast = $railsAround[Vector3::SIDE_EAST];
				$damage = $this->connectMultiples($railSouth, Vector3::SIDE_SOUTH, $railEast, Vector3::SIDE_EAST);
				$this->setDamage($damage->getDamage());
			}else{
				$railSouth = $railsAround[Vector3::SIDE_EAST];
				$railEast = $railsAround[Vector3::SIDE_WEST];
				$damage = $this->connectMultiples($railSouth, Vector3::SIDE_EAST, $railEast, Vector3::SIDE_WEST);
				$this->setDamage($damage->getDamage());
			}
		}elseif($railSides !== 0){
			if($this->canBeCurved()){
				// 2 Rails been placed
				// - + -   => '+' is the placed block
				if($railSides === 2){
					$rail1 = $rails[0];
					$rail2 = $rails[1];
					$damage = $this->connectMultiples($rail1, $sides[0], $rail2, $sides[1]);
					$this->setDamage($damage->getDamage());
				}else{
					$rail = [];

					// Curves see: wiki#Placement
					$curves = [self::CURVED_SOUTH_EAST, self::CURVED_NORTH_EAST, self::CURVED_SOUTH_WEST, self::CURVED_NORTH_WEST];
					foreach($curves as $side){
						$railTemp = [];
						$origin = Orientation::byMetadata($side);
						foreach($origin->connectingDirections() as $sided){
							if(!isset($railsAround[$sided])){
								$railTemp = [];
								break;
							}else{
								$railTemp = array_values($origin->connectingDirections());
							}
						}
						if(!empty($railTemp)){
							$rail = $railTemp;
						}
					}

					$railSouth = $railsAround[$rail[0]];
					$railEast = $railsAround[$rail[1]];
					$damage = $this->connectMultiples($railSouth, $rail[0], $railEast, $rail[1]);
					$this->setDamage($damage->getDamage());
				}
			}else{
				// TODO: Support redstone powered rails
			}
		}

		// If there are no other rails adjacent it will be
		// placed as a straight track oriented north-south.
		$this->getLevel()->setBlock($this, $this);
		return true;
	}

	/**
	 * Get the rails around the adjacent block.
	 * This will only return the blocks that with its
	 * horizontal sides.
	 *
	 * @return Rail[]
	 */
	private function checkRailsAroundAffected(): array{
		$array = [];
		$sides = [Vector3::SIDE_NORTH, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST, Vector3::SIDE_EAST];
		$railsAround = $this->checkRailsAround($sides);
		foreach($railsAround as $side => $rail){
			if(count($rail->checkRailsConnected()) != 2){
				$array[$side] = $rail;
			}
		}

		return $array;
	}

	/**
	 * @param array $faces
	 * @return Rail[]
	 */
	private function checkRailsAround(array $faces){
		$result = [];
		foreach($faces as $side){
			$block = $this->getSide($side);
			$up = $block->getSide(Vector3::SIDE_UP);
			$down = $block->getSide(Vector3::SIDE_DOWN);
			if($up instanceof Rail){
				$result[$side] = $up;
			}
			if($down instanceof Rail){
				$result[$side] = $down;
			}
			if($block instanceof Rail){
				$result[$side] = $block;
			}
		}

		return $result;
	}

	/**
	 * @return Rail[]
	 */
	public function checkRailsConnected(): array{
		$result = [];
		$origin = $this->getOrientation()->connectingDirections();
		$railsAround = $this->checkRailsAround($origin);

		foreach($railsAround as $side => $rail){
			if($rail->getOrientation()->hasConnectingDirections(Vector3::getOppositeSide($side))){
				$result[$side] = $rail;
			}
		}

		return $result;
	}


	public function getOrientation(): Orientation{
		return self::$railMetadata[$this->getDamage()];
	}

	/**
	 * Connects to a rail and return the specific orientation
	 * for the connection.
	 *
	 * @param Rail $other The rail class itself
	 * @param int $face Faces of the rail
	 * @return Orientation The orientation that should be changed with this rail.
	 */
	private function connectTo(Rail $other, int $face): Orientation{
		$delta = $this->y - $other->y;
		$rails = $other->checkRailsConnected();
		if(empty($rails)){
			$other->setOrientation($delta === 1 ? Orientation::getAscendingData(Vector3::getOppositeSide($face)) : Orientation::getNormalRail($face));

			return $delta === -1 ? Orientation::getAscendingData($face) : Orientation::getNormalRail($face);
		}elseif(count($rails) === 1){
			foreach($rails as $faceConnected => $railData){
				// Set the rail to be curved
				if($other->canBeCurved() && $faceConnected !== $face){
					$other->setOrientation(Orientation::getCurvedState(Vector3::getOppositeSide($face), $faceConnected));

					return $delta === -1 ? Orientation::getAscendingData($face) : Orientation::getNormalRail($face);
				}elseif($faceConnected === $face){
					if(!$other->getOrientation()->isAscending()){
						$other->setOrientation($delta === 1 ? Orientation::getAscendingData(Vector3::getOppositeSide($face)) : Orientation::getNormalRail($face));
					}

					return $delta === -1 ? Orientation::getAscendingData($face) : Orientation::getNormalRail($face);
				}elseif($other->getOrientation()->hasConnectingDirections(Vector3::SIDE_NORTH, Vector3::SIDE_NORTH)){
					$other->setOrientation($delta === 1 ? Orientation::getAscendingData(Vector3::getOppositeSide($face)) : Orientation::getNormalRail($face));

					return $delta === -1 ? Orientation::getAscendingData($face) : Orientation::getNormalRail($face);
				}
				break;
			}
		}

		return self::$railMetadata[self::STRAIGHT_NORTH_SOUTH];
	}

	public function setOrientation(Orientation $origin){
		if($origin->getDamage() != $this->getDamage()){
			$this->setDamage($origin->getDamage());
			$this->getLevel()->setBlock($this, $this, true, true);
		}
	}

	/**
	 * This checks if the rail could be
	 * curved or not.
	 *
	 * @return bool
	 */
	public function canBeCurved(): bool{
		return true;
	}

	/**
	 * Connect to a multiple rail once at a time.
	 * And return an orientation that should be intersect with
	 * these rails.
	 *
	 * @param Rail $rail1 The rail class itself
	 * @param int $face1 The rail1 orientation
	 * @param Rail $rail2 The rail class itself
	 * @param int $face2 The rail2 orientation
	 * @return Orientation The orientation that should be given after the intersection.
	 */
	public function connectMultiples(Rail $rail1, int $face1, Rail $rail2, int $face2): Orientation{
		$this->connectTo($rail1, $face1);
		$this->connectTo($rail2, $face2);

		if(Vector3::getOppositeSide($face1) === $face2){
			$delta1 = $this->y = $rail1->y;
			$delta2 = $this->y = $rail2->y;

			if($delta1 === -1){
				return Orientation::getAscendingData($face1);
			}elseif($delta2 === -2){
				return Orientation::getAscendingData($face2);
			}
		}

		return Orientation::getConnectedState($face1, $face2);
	}
}
