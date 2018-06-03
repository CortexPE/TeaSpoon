<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 * Credits to: https://github.com/thebigsmileXD/SimpleSpawner
 * Modded to make it more vanilla-like and fix some logical bugs
 *
*/

declare(strict_types = 1);

namespace CortexPE\block;

use CortexPE\Main;
use CortexPE\tile\MobSpawner;
use pocketmine\block\{
	Block, MonsterSpawner as SpawnerPM
};
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\{
	CompoundTag, IntTag, StringTag
};
use pocketmine\Player;
use pocketmine\tile\Tile;

class MonsterSpawner extends SpawnerPM {

	/** @var int $entityid */
	private $entityid = 0;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated(): bool{
		return true;
	}

	/**
	 * @param Item $item
	 * @param Player|null $player
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null): bool{
		if(Main::$mobSpawnerEnable){
			if($this->entityid != 0 || $item->getId() != Item::SPAWN_EGG) return false;
			$tile = $this->getLevel()->getTile($this);
			$this->entityid = $item->getDamage();
			if(!$tile instanceof MobSpawner){
				/** @var CompoundTag $nbt */
				$nbt = new CompoundTag("", [
					new StringTag(Tile::TAG_ID, Tile::MOB_SPAWNER),
					new IntTag(Tile::TAG_X, (int)$this->x),
					new IntTag(Tile::TAG_Y, (int)$this->y),
					new IntTag(Tile::TAG_Z, (int)$this->z),
				]);
				/** @var MobSpawner $tile */
				$tile = Tile::createTile(Tile::MOB_SPAWNER, $this->getLevel(), $nbt);
				$tile->setEntityId($this->entityid);

				return true;
			}
		}

		return true;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
		parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);

		if($item->getDamage() > 9 && Main::$mobSpawnerEnable && Main::$mobSpawnerDamageAsEID){
			$tile = $this->getLevel()->getTile($this);
			$this->entityid = $item->getDamage();
			$this->meta = 0;
			$this->getLevel()->setBlock($this, $this, true, false);
			if(!$tile instanceof MobSpawner){
				/** @var CompoundTag $nbt */
				$nbt = new CompoundTag("", [
					new StringTag(Tile::TAG_ID, Tile::MOB_SPAWNER),
					new IntTag(Tile::TAG_X, (int)$this->x),
					new IntTag(Tile::TAG_Y, (int)$this->y),
					new IntTag(Tile::TAG_Z, (int)$this->z),
				]);
				/** @var MobSpawner $tile */
				$tile = Tile::createTile(Tile::MOB_SPAWNER, $this->getLevel(), $nbt);
				$tile->setEntityId($this->entityid);

				return true;
			}
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

	public function getSilkTouchDrops(Item $item): array{
		return [];
	}

	public function onBreak(Item $item, Player $player = \null): bool{
		$parent = parent::onBreak($item, $player);
		if(Main::$silkSpawners && $item->hasEnchantment(Enchantment::SILK_TOUCH)){
			$tile = $this->getLevel()->getTile($this->asVector3());
			if($tile instanceof MobSpawner){
				$this->getLevel()->dropItem($this->add(0.5, 0.5,0.5), Item::get(Item::MOB_SPAWNER, $tile->getEntityId(), 1));
			}
		}
		return $parent;
	}
}
