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

use CortexPE\tile\MobSpawner;
use pocketmine\block\{
	Block, MonsterSpawner as SpawnerPM
};
use pocketmine\item\{
	enchantment\Enchantment, Item
};
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\{
	CompoundTag, IntTag, StringTag
};
use pocketmine\Player;
use pocketmine\tile\Tile;

class MonsterSpawner extends SpawnerPM {

	const EID_TO_STR = [
		10  => "Chicken",
		11  => "Cow",
		12  => "Pig",
		13  => "Sheep",
		14  => "Wolf",
		15  => "Villager",
		16  => "Mooshroom",
		17  => "Squid",
		18  => "Rabbit",
		19  => "Bat",
		20  => "Iron Golem",
		21  => "Snow Golem",
		22  => "Ocelot",
		23  => "Horse",
		24  => "Donkey",
		25  => "Mule",
		26  => "Skeleton Horse",
		27  => "Zombie Horse",
		28  => "Polar Bear",
		29  => "Llama",
		30  => "Parrot",
		32  => "Zombie",
		33  => "Creeper",
		34  => "Skeleton",
		35  => "Spider",
		36  => "Zombie Pigman",
		37  => "Slime",
		38  => "Enderman",
		39  => "Silverfish",
		40  => "Cave Spider",
		41  => "Ghast",
		42  => "Magma Cube",
		43  => "Blaze",
		44  => "Zombie Villager",
		45  => "Witch",
		46  => "Stray",
		47  => "Husk",
		48  => "Wither Skeleton",
		49  => "Guardian",
		50  => "Elder Guardian",
		51  => "NPC",
		52  => "Wither",
		53  => "Ender Dragon",
		54  => "Shulker",
		55  => "Endermite",
		56  => "Agent",
		57  => "Vindicator",
		61  => "Armor Stand",
		62  => "Tripod Camera",
		63  => "Player",
		64  => "Item",
		65  => "TNT",
		66  => "Falling Block",
		67  => "Moving Block",
		68  => "XP Bottle",
		69  => "XP Orb",
		70  => "Eye of Ender signal",
		71  => "Endercrystal",
		72  => "Fireworks Rocket",
		76  => "Shulker Bullet",
		77  => "Fishing Hook",
		78  => "Chalkboard",
		79  => "Dragon Fireball",
		80  => "Arrow",
		81  => "Snowball",
		82  => "Egg",
		83  => "Painting",
		84  => "Minecart",
		85  => "Large Fireball",
		86  => "Splash Potion",
		87  => "Ender Pearl",
		88  => "Leash Knot",
		89  => "Wither Skull",
		90  => "Boat",
		91  => "Wither Skull Dangerous",
		93  => "Lightning Bolt",
		94  => "Small Fireball",
		95  => "Area Effect Cloud",
		96  => "Hopper Minecart",
		97  => "TNT Minecart",
		98  => "Chest Minecart",
		100 => "Command Block Minecart",
		101 => "Lingering Potion",
		102 => "Llama Spit",
		103 => "evocation Fang",
		104 => "Evocation Illager",
		105 => "Vex",
	];

	/** @var int $entityid */
	private $entityid = 0;

	public function __construct(){
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
		return true;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
		parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);

		if($item->getDamage() <= 0) return false;
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
		return true;
	}

	/**
	 * @param Item $item
	 * @return array
	 */
	public function getDrops(Item $item): array{
		$tile = $this->getLevel()->getTile($this);
		if(!$tile instanceof MobSpawner) return [];
		if($item->hasEnchantment(Enchantment::SILK_TOUCH)){
			return [
				Item::get($this->getItemId(), (int)$tile->getEntityId(), 1, $this->getLevel()->getTile($this)->namedtag),
			];
		}

		return [];
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		if($this->entityid === 0) return "Monster Spawner";
		else{
			$name = ucfirst(self::EID_TO_STR[$this->entityid] ?? 'Monster') . ' Spawner';

			return $name;
		}
	}
}