<?php

/*
 *
 *  _____            _               _____
 * / ____|          (_)             |  __ \
 *| |  __  ___ _ __  _ ___ _   _ ___| |__) | __ ___
 *| | |_ |/ _ \ '_ \| / __| | | / __|  ___/ '__/ _ \
 *| |__| |  __/ | | | \__ \ |_| \__ \ |   | | | (_) |
 * \_____|\___|_| |_|_|___/\__, |___/_|   |_|  \___/
 *                         __/ |
 *                        |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author GenisysPro
 * @link https://github.com/GenisysPro/GenisysPro
 *
 *
*/

declare(strict_types = 1);

namespace CortexPE\block;

use CortexPE\tile\{
	EnderChest as TileEnderChest, Tile
};
use pocketmine\block\{
	Block, Transparent
};
use pocketmine\item\{
	enchantment\Enchantment, Item, Tool
};
use pocketmine\math\{
	AxisAlignedBB, Vector3
};
use pocketmine\nbt\tag\{
	CompoundTag, IntTag, StringTag
};
use pocketmine\Player;

class EnderChest extends Transparent {

	protected $id = Block::ENDER_CHEST;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function canBeActivated(): bool{
		return true;
	}

	public function getHardness(): float{
		return 22.5;
	}

	public function getResistance(): float{
		return 3000;
	}

	public function getLightLevel(): int{
		return 7;
	}

	public function getName(): string{
		return "Ender Chest";
	}

	public function getToolType(): int{
		return Tool::TYPE_PICKAXE;
	}

	public function place(Item $item, Block $block, Block $target, $face, Vector3 $facePos, Player $player = null): bool{
		$faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3,
		];

		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];

		$this->getLevel()->setBlock($block, $this, true, true);

		$nbt = new CompoundTag("", [
			new StringTag("id", Tile::ENDER_CHEST),
			new IntTag("x", $this->x),
			new IntTag("y", $this->y),
			new IntTag("z", $this->z),
		]);

		if($item->hasCustomName()){
			$nbt->CustomName = new StringTag("CustomName", $item->getCustomName());
		}

		Tile::createTile("EnderChest", $this->getLevel(), $nbt);

		return true;
	}

	public function onActivate(Item $item, Player $player = null): bool{
		if($player instanceof Player){
			$top = $this->getSide(Vector3::SIDE_UP);
			if($top->isTransparent() !== true){
				return true;
			}

			if(!(($tile = $this->getLevel()->getTile($this)) instanceof TileEnderChest)){
				$nbt = new CompoundTag("", [
					new StringTag("id", Tile::ENDER_CHEST),
					new IntTag("x", $this->x),
					new IntTag("y", $this->y),
					new IntTag("z", $this->z),
				]);
				$tile = Tile::createTile("EnderChest", $this->getLevel(), $nbt);
			}

			if($player->isCreative()){
				return true;
			}

			if($tile instanceof TileEnderChest){
				// tnx https://github.com/RealDevs/TableSpoon
				$player->addWindow($tile->getInventory());
			}
		}

		return true;
	}

	public function getDrops(Item $item): array{
		if($item->hasEnchantment(Enchantment::SILK_TOUCH)){
			return [
				[$this->id, 0, 1],
			];
		}

		return [
			[Item::OBSIDIAN, 0, 8],
		];
	}

	protected function recalculateBoundingBox(): AxisAlignedBB{
		return new AxisAlignedBB(
			$this->x + 0.0625,
			$this->y,
			$this->z + 0.0625,
			$this->x + 0.9375,
			$this->y + 0.9475,
			$this->z + 0.9375
		);
	}

}