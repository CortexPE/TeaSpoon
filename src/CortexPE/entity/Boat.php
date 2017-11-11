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
 * @author ClearSky
 * @link https://github.com/ClearSkyTeam/PocketMine-MP
 *
*/

namespace CortexPE\entity;

use pocketmine\entity\Vehicle;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\Level;
use pocketmine\nbt\tag\{
	ByteTag, CompoundTag
};

class Boat extends Vehicle {

	const NETWORK_ID = self::BOAT;

	public $height = 0.7;
	public $width = 1.6;
	public $gravity = 0.5;
	public $drag = 0.1;

	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->WoodID)){
			$nbt->WoodID = new ByteTag("WoodID", 0);
		}
		parent::__construct($level, $nbt);
	}

	public function initEntity(){
		$this->setMaxHealth(4);
		parent::initEntity();
	}

	public function getDrops(): array{
		return [
			ItemItem::get(ItemItem::BOAT, $this->getWoodID(), 1),
		];
	}

	public function getWoodID(){
		return $this->namedtag["WoodID"];
	}
}