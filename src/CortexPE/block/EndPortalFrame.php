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

use pocketmine\block\Block;
use pocketmine\block\EndPortalFrame as PMEndPortalFrame;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class EndPortalFrame extends PMEndPortalFrame {
	public function __construct($meta = 0){
		parent::__construct($meta);
	}

	// Code below if ported from ClearSky (Big Thanks to XenialDan for Having the time to actually test it)
	public function place(Item $item, Block $block, Block $target, int $face, Vector3 $facePos, Player $player = null): bool{
		$faces = [
			0 => 3,
			1 => 0,
			2 => 1,
			3 => 2,
		];
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];
		$this->getLevel()->setBlock($block, $this, true, true);
		return true;
	}
	
	public function onActivate(Item $item, Player $player = null): bool{
		if (($this->getDamage() & 0x04) === 0 && $player instanceof Player && $item->getId() === Item::ENDER_EYE){
			$this->setDamage($this->getDamage() + 4);
			$this->getLevel()->setBlock($this, $this, true, true);
			$corners = $this->isValidPortal();
			if(is_array($corners)){
				$this->createPortal($corners);
			}
			return true;
		}
		return false;
	}
	
	public function isValidPortal() : array {
		// TODO: Portal Checks
		return [
			new Vector3(0,0,0), // corner 1
			new Vector3(0,0,0), // corner 2
			new Vector3(0,0,0), // corner 3
			new Vector3(0,0,0), // corner 4
		];
	}
	
	private function createPortal(array $corners = null){
		if($corners === null){
			return false;
		}
		// TODO: set the blocks based from dimensions
		return true;
	}
}