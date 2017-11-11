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

declare(strict_types = 1);

namespace CortexPE\item;

use CortexPE\entity\Boat as BoatEntity;
use pocketmine\block\Block;
use pocketmine\item\Item as ItemPM;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\{
	CompoundTag, DoubleTag, FloatTag, IntTag, ListTag
};
use pocketmine\Player;

class Boat extends ItemPM {

	public function __construct($meta = 0){
		parent::__construct(self::BOAT, $meta, "Oak Boat");
		if($this->meta === 1){
			$this->name = "Spruce Boat";
		}elseif($this->meta === 2){
			$this->name = "Birch Boat";
		}elseif($this->meta === 3){
			$this->name = "Jungle Boat";
		}elseif($this->meta === 4){
			$this->name = "Acacia Boat";
		}elseif($this->meta === 5){
			$this->name = "Dark Oak Boat";
		}
	}

	public function getMaxStackSize(): int{
		return 1;
	}

	public function canBeActivated(){
		return true;
	}

	public function onActivate(Level $level, Player $player, Block $block, Block $target, int $face, Vector3 $facepos): bool{
		$realPos = $target->getSide($face)->add(0.5, 0.4, 0.5);
		$boat = new BoatEntity($player->getLevel(), new CompoundTag("", [
			new ListTag("Pos", [
				new DoubleTag("", $realPos->getX()),
				new DoubleTag("", $realPos->getY()),
				new DoubleTag("", $realPos->getZ()),
			]),
			new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0),
			]),
			new ListTag("Rotation", [
				new FloatTag("", 0),
				new FloatTag("", 0),
			]),
			new IntTag("WoodID", $this->getDamage()),
		]));
		$boat->spawnToAll();
		if($player->isSurvival()){
			--$this->count;
		}

		return true;
	}

	public function getFuelTime(): int{
		return 1200; //400 in PC
	}
	//TODO
}