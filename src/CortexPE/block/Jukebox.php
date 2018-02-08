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

use CortexPE\item\Record;
use CortexPE\tile\Jukebox as JukeboxTile;
use CortexPE\tile\Tile;
use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\block\Solid;
use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;

class Jukebox extends Solid {

	/** @var int $id */
	protected $id = self::JUKEBOX;

	public function getName(): string{
		return "Jukebox";
	}

	public function __construct(int $meta = 0){
		parent::__construct(self::JUKEBOX, $meta);
	}

	public function getHardness(): float{
		return 2;
	}

	public function getToolType(): int{
		return BlockToolType::TYPE_AXE;
	}

	public function getToolHarvestLevel(): int{
		return TieredTool::TIER_WOODEN;
	}

	public function getDrops(Item $item): array{
		$drops = [];
		$drops[] = Item::get(Item::JUKEBOX, 0, 1);

		$tile = $this->getLevel()->getTile($this);
		if($tile instanceof JukeboxTile){
			$drops[] = $tile->getRecordItem();
		}

		return $drops;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
		$tile = $this->getLevel()->getTile($this);
		if(!($tile instanceof JukeboxTile)){
			$nbt = new CompoundTag("", [
				new StringTag(Tile::TAG_ID, Tile::JUKEBOX),
				new IntTag(Tile::TAG_X, (int) $this->getX()),
				new IntTag(Tile::TAG_Y, (int) $this->getY()),
				new IntTag(Tile::TAG_Z, (int) $this->getZ())
			]);
			Tile::createTile(Tile::JUKEBOX, $this->getLevel(), $nbt);
		}

		return true;
	}

	public function onActivate(Item $item, Player $player = null): bool{
		$tile = $this->getLevel()->getTile($this);
		if($tile instanceof JukeboxTile){
			$tile->dropMusicDisc();
			if($item instanceof Record){
				$tile->setRecordItem($item);
				$tile->playMusicDisc();
				if($player != null){
					$item->count--;
				}
			}
		} else {
			$nbt = new CompoundTag("", [
				new StringTag(Tile::TAG_ID, Tile::JUKEBOX),
				new IntTag(Tile::TAG_X, (int) $this->getX()),
				new IntTag(Tile::TAG_Y, (int) $this->getY()),
				new IntTag(Tile::TAG_Z, (int) $this->getZ())
			]);
			/** @var JukeboxTile $tile */
			$tile = Tile::createTile(Tile::JUKEBOX, $this->getLevel(), $nbt);

			if($item instanceof Record){
				$tile->setRecordItem($item);
				if($player != null){
					$item->count--;
				}
			}
		}

		return true;
	}
}
