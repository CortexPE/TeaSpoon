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

use CortexPE\inventory\BeaconInventory;
use CortexPE\Main;
use CortexPE\tile\{
	Beacon as TileBeacon, Tile
};
use pocketmine\block\{
	Air, Block, Transparent
};
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\{
	ByteTag, CompoundTag, IntTag, StringTag
};
use pocketmine\Player;

class Beacon extends Transparent {

	/**
	 * @var int
	 */
	protected $id = self::BEACON;

	/**
	 * Beacon constructor.
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated(): bool{
		return true;
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return "Beacon";
	}

	/**
	 * @return int
	 */
	public function getLightLevel(): int{
		return 15;
	}

	public function getBlastResistance(): float{
		return 15;
	}

	/**
	 * @return float
	 */
	public function getHardness(): float{
		return 3;
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
		$this->getLevel()->setBlock($this, $this, true, true);
		/** @var CompoundTag $nbt */
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

	/**
	 * @param Item $item
	 * @param Player|null $player
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null): bool{
		if(Main::$beaconEnabled){
			if(!$player instanceof Player) return false;
			/** @var Tile $t */
			$t = $this->getLevel()->getTile($this);
			/** @var BeaconInventory $beacon */
			$beacon = null;
			if($t instanceof TileBeacon){
				/** @var TileBeacon $beacon */
				$beacon = $t;
			}else{
				/** @var CompoundTag $nbt */
				$nbt = new CompoundTag("", [
					new StringTag("id", Tile::BEACON),
					new ByteTag("isMovable", 0),
					new IntTag("primary", 0),
					new IntTag("secondary", 0),
					new IntTag("x", $this->x),
					new IntTag("y", $this->y),
					new IntTag("z", $this->z),
				]);
				$beacon = Tile::createTile(Tile::BEACON, $this->getLevel(), $nbt);
			}
			if($player->isCreative() && Main::$limitedCreative){
				return true;
			}
			$inv = $beacon->getInventory();
			if($inv instanceof BeaconInventory){
				$player->addWindow($beacon->getInventory());
			}
		}

		return true;
	}

	/**
	 * @param Item $item
	 * @param Player|null $player
	 * @return bool
	 */
	public function onBreak(Item $item, Player $player = null): bool{
		$this->getLevel()->setBlock($this, new Air(), true, true);

		return true;
	}
}
