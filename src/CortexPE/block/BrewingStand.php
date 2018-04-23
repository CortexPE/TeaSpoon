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

use CortexPE\Main;
use CortexPE\tile\BrewingStand as BrewingStandTile;
use pocketmine\block\Block;
use pocketmine\block\BrewingStand as PMBrewingStand;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Tile;

class BrewingStand extends PMBrewingStand {
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = \null): bool{
		$parent = parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
		if(!$blockReplace->getSide(Vector3::SIDE_DOWN)->isTransparent()){
			// wtf?
			$nbt = new CompoundTag("", [
				new StringTag(Tile::TAG_ID, Tile::BREWING_STAND),
				new IntTag(Tile::TAG_X, (int)$this->x),
				new IntTag(Tile::TAG_Y, (int)$this->y),
				new IntTag(Tile::TAG_Z, (int)$this->z),
			]);
			$nbt->setInt(BrewingStandTile::TAG_BREW_TIME, BrewingStandTile::MAX_BREW_TIME);

			if($item->hasCustomName()){
				$nbt->setString("CustomName", $item->getCustomName());
			}
			new BrewingStandTile($player->getLevel(), $nbt);
		}

		return $parent;
	}

	public function getLightLevel(): int{
		return 1;
	}

	public function getBlastResistance(): float{
		return 2.5;
	}

	public function onActivate(Item $item, Player $player = \null): bool{
		if(!Main::$brewingStandsEnabled || (Main::$limitedCreative && $player->isCreative())){
			return true;
		}
		$parent = parent::onActivate($item, $player);
		$tile = $player->getLevel()->getTile($this);
		if($tile instanceof BrewingStandTile){
			$player->addWindow($tile->getInventory());
		}else{
			// still, WHAT THE FUCK?!
			$nbt = new CompoundTag("", [
				new StringTag(Tile::TAG_ID, Tile::BREWING_STAND),
				new IntTag(Tile::TAG_X, (int)$this->x),
				new IntTag(Tile::TAG_Y, (int)$this->y),
				new IntTag(Tile::TAG_Z, (int)$this->z),
			]);
			$nbt->setInt(BrewingStandTile::TAG_BREW_TIME, BrewingStandTile::MAX_BREW_TIME);

			if($item->hasCustomName()){
				$nbt->setString("CustomName", $item->getCustomName());
			}
			$tile = new BrewingStandTile($player->getLevel(), $nbt);
			$player->addWindow($tile->getInventory());
		}

		return $parent;
	}
}