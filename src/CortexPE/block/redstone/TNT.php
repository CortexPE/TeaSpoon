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
 *
*/

namespace CortexPE\block\redstone;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\Solid;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\sound\FizzSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\utils\Random;

class TNT extends Solid {

	protected $id = self::TNT;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return "TNT";
	}

	public function getHardness() : float {
		return 0;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated(): bool{
		return true;
	}

	/**
	 * @return int
	 */
	public function getBurnChance(): int{
		return 15;
	}

	/**
	 * @return int
	 */
	public function getBurnAbility(): int{
		return 100;
	}

	/**
	 * @param Player|null $player
	 */
	public function prime(Player $player = null){
		$this->meta = 1;
		if($player != null and $player->isCreative()){
			$dropItem = false;
		}else{
			$dropItem = true;
		}
		$mot = (new Random())->nextSignedFloat() * M_PI * 2;
		$tnt = Entity::createEntity("PrimedTNT", $this->getLevel(), new CompoundTag("", [
			"Pos"      => new ListTag("Pos", [
				new DoubleTag("", $this->x + 0.5),
				new DoubleTag("", $this->y),
				new DoubleTag("", $this->z + 0.5),
			]),
			"Motion"   => new ListTag("Motion", [
				new DoubleTag("", -sin($mot) * 0.02),
				new DoubleTag("", 0.2),
				new DoubleTag("", -cos($mot) * 0.02),
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", 0),
				new FloatTag("", 0),
			]),
			"Fuse"     => new ByteTag("Fuse", 80),
		]), $dropItem);

		$tnt->spawnToAll();
		$this->level->addSound(new FizzSound($this));
	}

	/**
	 * @param int $type
	 *
	 * @return bool|int
	 */
	public function onUpdate($type){
		if($type == Level::BLOCK_UPDATE_SCHEDULED){
			$sides = [0, 1, 2, 3, 4, 5];
			foreach($sides as $side){
				$block = $this->getSide($side);
				if($block instanceof RedstoneSource and $block->isActivated($this)){
					$this->prime();
					$this->getLevel()->setBlock($this, new Air(), true);
					break;
				}
			}

			return Level::BLOCK_UPDATE_SCHEDULED;
		}

		return false;
	}

	public function place(Item $item, Block $block, Block $target, $face, Vector3 $clickVector, Player $player = null) : bool {
		$this->getLevel()->setBlock($this, $this, true, false);

		$this->getLevel()->scheduleUpdate($this, 40);
		return true;
	}

	/**
	 * @param Item $item
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null) : bool {
		if($item->getId() === Item::FLINT_STEEL){
			$this->prime($player);
			$this->getLevel()->setBlock($this, new Air(), true);

			$item->useOn($this);

			return true;
		}

		return false;
	}
}
