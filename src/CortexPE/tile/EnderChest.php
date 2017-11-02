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

namespace CortexPE\tile;

use CortexPE\inventory\EnderChestInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Nameable;
use pocketmine\tile\Spawnable;

class EnderChest extends Spawnable implements InventoryHolder, Nameable {

	/**
	 * @return string
	 */
	public function getName(): string{
		return isset($this->namedtag->CustomName) ? $this->namedtag->CustomName->getValue() : "Ender Chest";
	}

	public function getDefaultName(): string{
		return "Ender Chest";
	}

	public function addAdditionalSpawnData(CompoundTag $nbt): void{
		if($this->hasName()){
			$nbt->CustomName = $this->namedtag->CustomName;
		}
	}

	/**
	 * @return bool
	 */
	public function hasName(): bool{
		return isset($this->namedtag->CustomName);
	}

	/**
	 * @param string $str
	 */
	public function setName(string $str){
		if($str === ""){
			unset($this->namedtag->CustomName);

			return;
		}

		$this->namedtag->CustomName = new StringTag("CustomName", $str);
	}

	public function getInventory(): EnderChestInventory{
		// tnx https://github.com/RealDevs/TableSpoon
		return new EnderChestInventory($this);
	}

}