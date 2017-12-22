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
use CortexPE\tile\ShulkerBox as TileShulkerBox;
use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Container;

class ShulkerBox extends Transparent {
	protected $id = self::SHULKER_BOX;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getResistance(): float{
		return 30;
	}

	public function getHardness(): float{
		return 6;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}
	public function getName() :string {
		return "Shulker Box";
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$this->getLevel()->setBlock($blockReplace, $this, true, true);

		$nbt = TileShulkerBox::createNBT($this->asVector3());
		$nbt->Items = $item->getNamedTag()->getListTag("Items") ?? new ListTag("Items", []);

		if($item->hasCustomName()){
			$nbt->CustomName = new StringTag("CustomName", $item->getCustomName());
		}
		/** @var TileShulkerBox $tile */
		Tile::createTile(Tile::SHULKER_BOX, $this->getLevel(), $nbt);

		if($player instanceof Player){ // remove this hack if pmmp adds a way to set the max stack size for blocks...
			$player->getInventory()->setItemInHand(Item::get(Item::AIR));
		}
		return true;
	}

	public function onBreak(Item $item, Player $player = null) : bool{
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

	public function onActivate(Item $item, Player $player = null) : bool{
		if($player instanceof Player){

			$t = $this->getLevel()->getTile($this);
			$sb = null;
			if($t instanceof TileShulkerBox){
				$sb = $t;
			}else{
				$nbt = TileShulkerBox::createNBT($this->asVector3());
				$nbt->Items = new ListTag("Items", []);

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

	public function getDrops(Item $item): array{
		return [];
	}
}