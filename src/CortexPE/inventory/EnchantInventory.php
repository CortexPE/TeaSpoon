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

namespace CortexPE\inventory;

use pocketmine\inventory\EnchantInventory as PMEnchantInventory;
use pocketmine\Player;

class EnchantInventory extends PMEnchantInventory {
	// TODO: Add Enchantment verification (if possible)
	public $random = null;

	public $bookshelfAmount = 0;

	public $levels = null;
	public $entries = null;

	/*
		public function onOpen(Player $who): void{
			parent::onOpen($who);
			$holder = $this->getHolder();
			if($holder instanceof EnchantingTable){
				if($this->levels == null){
					$this->levels = [];
					$this->bookshelfAmount = $holder->countBookshelf();

					if($this->bookshelfAmount < 0){
						$this->bookshelfAmount = 0;
					}

					if($this->bookshelfAmount > 15){
						$this->bookshelfAmount = 15;
					}

					$random = new Random();
					$base = (double)$random->nextRange(1, 8) + ($this->bookshelfAmount / 2) + (double)$random->nextRange(0, $this->bookshelfAmount);
					$this->levels[0] = (int)max($base / 3, 1);
					$this->levels[1] = (int)(($base * 2) / 3 + 1);
					$this->levels[2] = (int)max($base, $this->bookshelfAmount * 2);
				}
			}
		}*/

	public function onClose(Player $who): void{
		$this->dropContents($this->holder->getLevel(), $this->holder->add(0.5, 0.5, 0.5));

		return;
	}
}