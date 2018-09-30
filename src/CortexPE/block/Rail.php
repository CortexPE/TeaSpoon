<?php
/**
 * BSD 3-Clause License
 *
 * Copyright (c) 2018, Adam Matthew, Hyrule Minigame Division
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * - Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace CortexPE\block;


use CortexPE\utils\Orientation;
use pocketmine\block\Block;
use pocketmine\block\Rail as PMRail;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Rail extends PMRail {

	protected $id = self::RAIL;

	// Credits: Nukkit
	// Actually this is a project of mine
	// But the other wrote it so ¯\_(ツ)_/¯
	protected $canBePowered = false;

	// One stop wiki-page: http://minecraft.gamepedia.com/Rail
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
		$down = $this->getSide(Vector3::SIDE_DOWN);
		if(is_null($down) || $down->isTransparent()){
			return false;
		}
		$railsAround = $this->checkRailsAroundAffected();
		/** @var Rail[] $rails */
		$rails = array_values($railsAround);
		/** @var int[] $faces */
		$faces = array_keys($railsAround);
		var_dump($railsAround);
		var_dump($rails);
		var_dump($faces);
		if(count($railsAround) == 1){
			/** @var Rail $other */
			$other = $rails[0];
			$this->setDamage($this->connect($other, $faces[0])->getDamage());
		}elseif(count($railsAround) == 4){
			if($this->isNormalRail()){
				$this->setDamage($this->connect(
					$rails[array_search(Vector3::SIDE_SOUTH, $faces)], Vector3::SIDE_SOUTH,
					$rails[array_search(Vector3::SIDE_EAST, $faces)], Vector3::SIDE_EAST)->getDamage());
			}else{
				$this->setDamage($this->connect(
					$rails[array_search(Vector3::SIDE_EAST, $faces)], Vector3::SIDE_EAST,
					$rails[array_search(Vector3::SIDE_SOUTH, $faces)], Vector3::SIDE_SOUTH)->getDamage());
			}
		}elseif(!empty($railsAround)){

		}
		$this->level->setBlock($this, $this, true, true);

		return true;
	}

	/**
	 * Get all the rails that effected to this
	 * affected to this rail.
	 *
	 * @return Rail[]
	 */
	public function checkRailsAroundAffected(){
		$railsAround = $this->checkRailsAround([Vector3::SIDE_SOUTH, Vector3::SIDE_EAST, Vector3::SIDE_WEST, Vector3::SIDE_NORTH]);
		if(count($this->checkRailsConnected()) != 2){
			return [];
		}

		return $railsAround;
	}

	/**
	 * @param int[] $directions
	 * @return Rail[]
	 */
	public function checkRailsAround(array $directions){
		$list = [];
		foreach($directions as $dir){
			// TODO: Improve the array keys to make it more easier access them
			$b = $this->getSide($dir);
			if(!($b instanceof Rail)){
				continue;
			}
			$list[$dir] = $b;
		}

		return $list;
	}

	/**
	 * @return Rail[]
	 */
	private function checkRailsConnected(){
		$railsAround = $this->checkRailsAround($this->getOrientation()->connectingDirections());
		$connectedRails = [];

		foreach($railsAround as $dir => $rail){
			if(!$rail->getOrientation()->hasConnectingDirections(Vector3::getOppositeSide($dir))){
				continue;
			}
			$connectedRails[$dir] = $rail;
		}

		return $connectedRails;
	}

	public function getOrientation(){
		return Orientation::byMetadata($this->getVariant());
	}

	private function connect(Rail $rail1, int $face1, Rail $rail2 = null, int $face2 = -1): Orientation{
		if(!is_null($rail2)){
			$this->connect($rail1, $face1);
			$this->connect($rail2, $face2);

			if(Vector3::getOppositeSide($face1) == $face2){
				$delta1 = $this->y - $rail1->y;
				$delta2 = $this->y - $rail2->y;

				if($delta1 == -1){
					return Orientation::ascending($face1);
				}elseif($delta2 == -1){
					return Orientation::ascending($face2);
				}
			}

			return Orientation::straightOrCurved($face1, $face2);
		}
		$delta = $this->y - $rail1->y;
		$rails = $rail1->checkRailsConnected();
		if(empty($rails)){
			$ori = $delta == 1 ? Orientation::ascending(Vector3::getOppositeSide($face1)) : Orientation::straight($face1);
			$rail1->setOrientation($ori);

			return $delta == -1 ? Orientation::ascending($face1) : Orientation::straight($face1);
		}elseif(count($rails) == 1){
			$faceConnected = array_rand($rails); // Pick a random rails

			// If you have sensitive butts, close them, because
			// I am going to do some DAMAGE, ready?
			if($rail1->isNormalRail() && $faceConnected != $face1){ // Curve them
				$ori = $delta == -1 ? Orientation::ascending($face1) : Orientation::straight($face1);
				$rail1->setOrientation(Orientation::curved(Vector3::getOppositeSide($face1), $faceConnected));

				return $ori;
			}elseif($faceConnected == $face1){
				if(!$rail1->getOrientation()->isAscending()){
					$rail1->setOrientation($delta == 1 ? Orientation::ascending(Vector3::getOppositeSide($face1)) : Orientation::straight($face1));
				}

				return $delta == -1 ? Orientation::ascending($face1) : Orientation::straight($face1);
			}elseif($rail1->getOrientation()->hasConnectingDirections(Vector3::SIDE_NORTH, Vector3::SIDE_SOUTH)){
				$ori = $delta == 1 ? Orientation::ascending(Vector3::getOppositeSide($face1)) : Orientation::straight($face1);
				$rail1->setOrientation($ori);

				return $delta == -1 ? Orientation::ascending($face1) : Orientation::straight($face1);
			}
		}

		return Orientation::byMetadata(Orientation::STRAIGHT_NORTH_SOUTH);
	}

	public function setOrientation(Orientation $ori){
		if($ori->getDamage() != $this->getVariant()){
			$this->setDamage($ori->getDamage());
			$this->level->setBlock($this, $this, false, true);
		}
	}

	public function isNormalRail(){
		return $this->getId() === self::RAIL;
	}

}