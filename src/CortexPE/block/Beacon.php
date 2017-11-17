<?php

/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author SuperXingKong
 *
 */

declare(strict_types = 1);

namespace CortexPE\block;

use CortexPE\Main;
use CortexPE\tile\Beacon as TileBeacon;
use CortexPE\tile\Tile;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;

class Beacon extends Transparent {

	protected $id = self::BEACON;

	/**
	 * Beacon constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function canBeActivated(): bool{
		return true;
	}

	public function getName(): string{
		return "Beacon";
	}

	public function getLightLevel(): int{
		return 15;
	}

	public function getResistance(): float{
		return 15;
	}

	public function getHardness(): float{
		return 3;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
		$this->getLevel()->setBlock($this, $this, true, true);
		$nbt = new CompoundTag("", [
			new StringTag("id", Tile::BEACON),
			new ByteTag("isMovable", 0),
			new IntTag("primary", 0),
			new IntTag("secondary", 0),
			new IntTag("x", $blockReplace->x),
			new IntTag("y", $blockReplace->y),
			new IntTag("z", $blockReplace->z),
		]);
		Tile::createTile(Tile::BEACON, $this->getLevel(), $nbt);

		return true;
	}

	public function onActivate(Item $item, Player $player = null): bool{
		if($player instanceof Player){
			$t = $this->getLevel()->getTile($this);
			$beacon = null;
			if($t instanceof TileBeacon){
				$beacon = $t;
			}else{
				$nbt = new CompoundTag("", [
					new StringTag("id", Tile::BEACON),
					new ByteTag("isMovable", 0),
					new IntTag("primary", 0),
					new IntTag("secondary", 0),
					new IntTag("x", $this->x),
					new IntTag("y", $this->y),
					new IntTag("z", $this->z),
				]);
				Tile::createTile(Tile::BEACON, $this->getLevel(), $nbt);
			}

			if($player->isCreative() && Main::$limitedCreative){
				return true;
			}

			$player->addWindow($beacon->getInventory());
		}

		return true;
	}


	public function onBreak(Item $item, Player $player = null): bool{
		$this->getLevel()->setBlock($this, new Air(), true, true);

		return true;
	}

}
