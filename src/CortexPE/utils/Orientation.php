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

namespace CortexPE\utils;


use pocketmine\math\Vector3;

/**
 * INTERNAL helper for Railway
 * <p>
 * By lmlstarqaq http://snake1999.com/
 * Rewrite by larryTheCoder
 * @package CortexPE\utils
 */
class Orientation {

	/** @var int Stores the direction of the rail */
	const STRAIGHT_NORTH_SOUTH = 0,
		STRAIGHT_EAST_WEST = 1,
		ASCENDING_EAST = 2,
		ASCENDING_WEST = 3,
		ASCENDING_NORTH = 4,
		ASCENDING_SOUTH = 5,
		CURVED_SOUTH_EAST = 6,
		CURVED_SOUTH_WEST = 7,
		CURVED_NORTH_WEST = 8,
		CURVED_NORTH_EAST = 9;

	/** @var int Stores the shape of a rail */
	const STRAIGHT = 0,
		ASCENDING = 1,
		CURVED = 2;

	/** @var Orientation[] */
	private static $railMetadata;

	private $meta;
	private $state;
	private $connectingDirections;
	private $ascendingDirection;

	public function __construct(int $meta, int $state, int $from, int $to, ?int $ascendingDirection){
		$this->meta = $meta;
		$this->state = $state;
		$this->connectingDirections[] = $from;
		$this->connectingDirections[] = $to;
		$this->ascendingDirection = $ascendingDirection;
	}

	/**
	 * Starts the orientation module
	 */
	public static final function startup(){
		self::$railMetadata[0] = new Orientation(0, self::STRAIGHT, Vector3::SIDE_NORTH, Vector3::SIDE_SOUTH, null);
		self::$railMetadata[1] = new Orientation(1, self::STRAIGHT, Vector3::SIDE_EAST, Vector3::SIDE_WEST, null);
		self::$railMetadata[2] = new Orientation(2, self::ASCENDING, Vector3::SIDE_EAST, Vector3::SIDE_WEST, Vector3::SIDE_EAST);
		self::$railMetadata[3] = new Orientation(3, self::ASCENDING, Vector3::SIDE_EAST, Vector3::SIDE_WEST, Vector3::SIDE_WEST);
		self::$railMetadata[4] = new Orientation(4, self::ASCENDING, Vector3::SIDE_NORTH, Vector3::SIDE_SOUTH, Vector3::SIDE_NORTH);
		self::$railMetadata[5] = new Orientation(5, self::ASCENDING, Vector3::SIDE_NORTH, Vector3::SIDE_SOUTH, Vector3::SIDE_SOUTH);
		self::$railMetadata[6] = new Orientation(6, self::CURVED, Vector3::SIDE_SOUTH, Vector3::SIDE_EAST, null);
		self::$railMetadata[7] = new Orientation(7, self::CURVED, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST, null);
		self::$railMetadata[8] = new Orientation(8, self::CURVED, Vector3::SIDE_NORTH, Vector3::SIDE_WEST, null);
		self::$railMetadata[9] = new Orientation(9, self::CURVED, Vector3::SIDE_NORTH, Vector3::SIDE_EAST, null);
	}

	public static function byMetadata(int $meta): Orientation{
		if($meta < 0 || $meta >= count(self::$railMetadata)){
			$meta = 0;
		}

		return self::$railMetadata[$meta];
	}

	public static function straight(int $face): Orientation{
		switch($face){
			case Vector3::SIDE_NORTH:
			case Vector3::SIDE_SOUTH:
				return self::$railMetadata[self::STRAIGHT_NORTH_SOUTH];
			case Vector3::SIDE_EAST:
			case Vector3::SIDE_WEST:
				return self::$railMetadata[self::STRAIGHT_EAST_WEST];
		}

		return self::$railMetadata[self::STRAIGHT_NORTH_SOUTH];
	}

	public static function ascending(int $face): Orientation{
		switch($face){
			case Vector3::SIDE_NORTH:
				return self::$railMetadata[self::ASCENDING_NORTH];
			case Vector3::SIDE_SOUTH:
				return self::$railMetadata[self::ASCENDING_SOUTH];
			case Vector3::SIDE_EAST:
				return self::$railMetadata[self::ASCENDING_EAST];
			case Vector3::SIDE_WEST:
				return self::$railMetadata[self::ASCENDING_WEST];
		}

		return self::$railMetadata[self::ASCENDING_EAST];
	}

	public static function curved(int $f1, int $f2): Orientation{
		foreach(self::$railMetadata as $o){
			if(!($o->meta >= 6 && $o->meta <= 9)){
				continue;
			}
			if($o->connectingDirections[$f1] && $o->connectingDirections[$f2]){
				return $o;
			}
		}

		return self::$railMetadata[self::CURVED_SOUTH_EAST];
	}

	public static function straightOrCurved(int $f1, int $f2): Orientation{
		foreach(self::$railMetadata as $o){
			if(!($o->meta >= 2 && $o->meta <= 5)){
				continue;
			}

			if($o->connectingDirections[$f1] && $o->connectingDirections[$f2]){
				return $o;
			}
		}

		return self::$railMetadata[self::STRAIGHT_NORTH_SOUTH];
	}


	public function metadata(): int{
		return $this->meta;
	}

	public function hasConnectingDirections(int... $faces): bool{
		// That's deep
		foreach($faces as $direction){
			foreach(self::$railMetadata as $rail){
				if(isset($rail->connectingDirections[$direction])){
					return true;
				}
			}
		}

		return false;
	}

	public function connectingDirections(){
		return $this->connectingDirections;
	}

	public function ascendingDirection(){
		return $this->ascendingDirection;
	}


	public function isStraight(): bool{
		return $this->state == self::STRAIGHT;
	}

	public function isAscending(): bool{
		return $this->state == self::ASCENDING;
	}

	public function isCurved(): bool{
		return $this->state == self::CURVED;
	}
}