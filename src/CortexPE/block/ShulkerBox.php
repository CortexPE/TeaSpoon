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
use CortexPE\tile\{
	ShulkerBox as TileShulkerBox, Tile
};
use pocketmine\block\{
	Block, BlockToolType, Transparent
};
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\{
	CompoundTag, ListTag, StringTag
};
use pocketmine\Player;
use pocketmine\tile\Container;

class ShulkerBox extends Transparent {

	/** @var int $id */
	protected $id = self::SHULKER_BOX;

	/**
	 * ShulkerBox constructor.
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return float
	 */
	public function getResistance(): float{
		return 30;
	}

	/**
	 * @return float
	 */
	public function getHardness(): float{
		return 6;
	}

	/**
	 * @return int
	 */
	public function getToolType(): int{
		return BlockToolType::TYPE_PICKAXE;
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return "Shulker Box";
	}

	/**
	 * @param Item $item
	 * @param Block $blockReplace
	 * @param Block $blockClicked
	 * @param int $face
	 * @param Vector3 $clickVector
	 * @param Player|null $player
	 * @return bool
	 */
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
		$this->getLevel()->setBlock($blockReplace, $this, true, true);
		/** @var CompoundTag $nbt */
		$nbt = TileShulkerBox::createNBT($this->asVector3());
		if($item->getNamedTag()->hasTag("Items", ListTag::class)){
			$nbt->setTag($item->getNamedTag()->getListTag("Items"));
		} else {
			$nbt->setTag(new ListTag("Items", []));
		}
		if($item->hasCustomName()){
			$nbt->setString("CustomName", $item->getCustomName());
		}
		/** @var TileShulkerBox $tile */
		Tile::createTile(Tile::SHULKER_BOX, $this->getLevel(), $nbt);
		$player->getInventory()->setItemInHand(Item::get(Item::AIR));

		return true;
	}

	/**
	 * @param Item $item
	 * @param Player|null $player
	 * @return bool
	 */
	public function onBreak(Item $item, Player $player = null): bool{
		/** @var TileShulkerBox $t */
		$t = $this->getLevel()->getTile($this);
		if($t instanceof TileShulkerBox){
			$item = Item::get(Item::SHULKER_BOX, $this->meta, 1);
			$itemNBT = clone $item->getNamedTag();
			$itemNBT->setTag($t->getNBT()->getTag(Container::TAG_ITEMS));
			$item->setNamedTag($itemNBT);
			$this->getLevel()->dropItem($this->asVector3(), $item);
			$t->getInventory()->clearAll(); // dont drop the items
		}
		$this->getLevel()->setBlock($this, Block::get(Block::AIR), true, true);

		return true;
	}

	/**
	 * @param Item $item
	 * @param Player|null $player
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null): bool{
		if(Main::$shulkerBoxEnabled){
			if(!$player instanceof Player) return false;
			$t = $this->getLevel()->getTile($this);
			$sb = null;
			if($t instanceof TileShulkerBox){
				$sb = $t;
			}else{
				$nbt = TileShulkerBox::createNBT($this->asVector3());
				$nbt->setTag(new ListTag("Items", []));
				$sb = Tile::createTile(Tile::SHULKER_BOX, $this->getLevel(), $nbt);
			}
			if(!($this->getSide(Vector3::SIDE_UP)->isTransparent()) or ($sb->namedtag->hasTag("Lock", StringTag::class) and $sb->namedtag->getString("Lock") !== $item->getCustomName())){
				return true;
			}
			if($player->isCreative() and Main::$limitedCreative){
				return true;
			}
			$player->addWindow($sb->getInventory());

		}
		return true;
	}

	/**
	 * @param Item $item
	 * @return array
	 */
	public function getDrops(Item $item): array{
		return [];
	}
}