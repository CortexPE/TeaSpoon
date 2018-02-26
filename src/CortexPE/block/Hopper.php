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
use CortexPE\tile\Tile;
use pocketmine\{
	block\Block, block\BlockToolType, block\Transparent, item\Item, math\Vector3, nbt\tag\CompoundTag, nbt\tag\IntTag, nbt\tag\ListTag, nbt\tag\StringTag, Player
};
use CortexPE\tile\Hopper as HopperTile;

class Hopper extends Transparent {
	protected $id = self::HOPPER_BLOCK;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function canBeActivated(): bool{
		return true;
	}

	public function getToolType(): int{
		return BlockToolType::TYPE_PICKAXE;
	}

	public function getName(): string{
		return "Hopper";
	}

	public function getHardness() : float{
		return 3;
	}

	public function getBlastResistance(): float{
		return 24;
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if(Main::$hoppersEnabled){
			if($player instanceof Player){
				$t = $this->getLevel()->getTile($this);
				if($t instanceof HopperTile){
					if($player->isCreative() and Main::$limitedCreative){
						return true;
					}
					$player->addWindow($t->getInventory());
				} else {
					$nbt = new CompoundTag("", [
						new ListTag("Items", []),
						new StringTag("id", Tile::HOPPER),
						new IntTag("x", $this->x),
						new IntTag("y", $this->y),
						new IntTag("z", $this->z),
					]);
					/** @var HopperTile $t */
					$t = Tile::createTile(Tile::HOPPER, $this->getLevel(), $nbt);
					if($player->isCreative() and Main::$limitedCreative){
						return true;
					}
					$player->addWindow($t->getInventory());
				}
			}
		}

		return true;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
		$faces = [
			0 => 0,
			1 => 0,
			2 => 3,
			3 => 2,
			4 => 5,
			5 => 4,
		];
		$this->meta = $faces[$face];
		$this->getLevel()->setBlock($blockReplace, $this, true, true);

		$nbt = new CompoundTag("", [
			new ListTag("Items", []),
			new StringTag("id", Tile::HOPPER),
			new IntTag("x", $this->x),
			new IntTag("y", $this->y),
			new IntTag("z", $this->z),
		]);

		if($item->hasCustomName()){
			$nbt->setString("CustomName", $item->getCustomName());
		}

		if($item->hasCustomBlockData()){
			foreach($item->getCustomBlockData() as $key => $v){
				$nbt->{$key} = $v;
			}
		}

		Tile::createTile(Tile::HOPPER, $this->getLevel(), $nbt);

		return true;
	}

	public function getDrops(Item $item): array{
		return [Item::get(Item::HOPPER, 0, 1)];
	}
}
