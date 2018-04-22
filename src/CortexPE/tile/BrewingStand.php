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

namespace CortexPE\tile;


use CortexPE\inventory\BrewingInventory;
use CortexPE\inventory\BrewingManager;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\tile\Container;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Nameable;
use pocketmine\tile\NameableTrait;
use pocketmine\tile\Spawnable;

class BrewingStand extends Spawnable implements InventoryHolder, Container, Nameable {
	use NameableTrait, ContainerTrait;

	public const TAG_BREW_TIME = "BrewTime";
	public const TAG_FUEL = "Fuel";
	public const TAG_HAS_BOTTLE_0 = "has_bottle_0";
	public const TAG_HAS_BOTTLE_1 = "has_bottle_1";
	public const TAG_HAS_BOTTLE_2 = "has_bottle_2";
	private const TAG_HAS_BOTTLE_BASE = "has_bottle_"; // lazy
	public const MAX_BREW_TIME = 400;
	public const MAX_FUEL = 20;

	private const INGREDIENTS = [
		Item::NETHER_WART,
		Item::GLOWSTONE_DUST,
		Item::REDSTONE,
		Item::FERMENTED_SPIDER_EYE,
		Item::MAGMA_CREAM,
		Item::SUGAR,
		Item::GLISTERING_MELON,
		Item::SPIDER_EYE,
		Item::GHAST_TEAR,
		Item::BLAZE_POWDER,
		Item::GOLDEN_CARROT,
		Item::PUFFERFISH,
		Item::RABBIT_FOOT,
		Item::GUNPOWDER,
	];

	/** @var BrewingInventory */
	private $inventory = null;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		if($nbt->hasTag(self::TAG_BREW_TIME, ShortTag::class)){
			$nbt->removeTag(self::TAG_BREW_TIME);
		}
		if($nbt->hasTag(self::TAG_FUEL, IntTag::class)){
			$nbt->removeTag(self::TAG_FUEL);
		}
		if(!$nbt->hasTag(self::TAG_BREW_TIME, IntTag::class)){
			$nbt->setInt(self::TAG_BREW_TIME, 0);
		}
		if(!$nbt->hasTag(self::TAG_FUEL, ByteTag::class)){
			$nbt->setByte(self::TAG_FUEL, 0);
		}

		$this->inventory = new BrewingInventory($this);

		$this->loadItems();
		$this->scheduleUpdate();
	}

	public function getRealInventory(){
		return $this->inventory;
	}

	public function getInventory(){
		return $this->inventory;
	}

	public function getDefaultName(): string{
		return "Brewing Stand";
	}

	public function addAdditionalSpawnData(CompoundTag $nbt): void{
		$nbt->setShort(self::TAG_BREW_TIME, self::MAX_BREW_TIME);
	}

	public function isValidIngredient(Item $item): bool{
		return (in_array($item->getId(), self::INGREDIENTS) && $item->getDamage() == 0);
	}

	// Ported and cleaned up from iTXTech/Genisys
	public function onUpdate(): bool{
		if($this->isClosed()){
			return false;
		}

		$return = false;
		$canBrew = false;

		$this->timings->startTiming();

		$fuel = $this->getInventory()->getFuel();
		$ingredient = $this->getInventory()->getIngredient();

		for($i = 1; $i <= 3; $i++){
			$hasBottle = false;
			$currItem = $this->inventory->getItem($i);
			if(in_array($currItem->getId(), [Item::POTION, Item::SPLASH_POTION])){
				$canBrew = true;
				$hasBottle = true;
			}
			$this->setBottle($i - 1, $hasBottle);
		}

		if($this->getFuelValue() <= 0){
			$fuel->count--;
			if($fuel->getCount() <= 0){
				$fuel = Item::get(Item::AIR);
			}
			$this->inventory->setFuel($fuel);
			$this->setFuelValue(self::MAX_FUEL);
		}
		$canBrew = (($fuel->getId() == Item::BLAZE_POWDER || $this->getFuelValue() > 0) && $canBrew);

		if(!$ingredient->isNull()){
			if($canBrew && $this->isValidIngredient($ingredient)){
				foreach($this->inventory->getPotions() as $potion){
					$recipe = BrewingManager::$instance->matchBrewingRecipe($ingredient, $potion);
					if($recipe !== null){
						$canBrew = true;
						break;
					}
					$canBrew = false;
				}
			}
		}else{
			$canBrew = false;
		}

		if($canBrew){
			$this->broadcastFuelTotal(self::MAX_FUEL);
			$return = true;
			$brewTime = $this->getBrewTime();
			$brewTime -= 1;
			$this->setBrewTime($brewTime);

			$this->broadcastBrewTime($brewTime);

			if($brewTime <= 0){
				for($i = 1; $i <= 3; $i++){
					$hasBottle = false;
					$potion = $this->inventory->getItem($i);
					$recipe = BrewingManager::$instance->matchBrewingRecipe($ingredient, $potion);
					if($recipe != null and !$potion->isNull()){
						$this->inventory->setItem($i, $recipe->getResult());
						$hasBottle = true;
					}
					$this->setBottle($i - 1, $hasBottle);
				}

				$ingredient->count--;
				if($ingredient->getCount() <= 0){
					$ingredient = Item::get(Item::AIR);
				}
				$this->inventory->setIngredient($ingredient);
				$this->saveItems();

				$fuelAmount = max($this->getFuelValue() - 1, 0);
				$this->setFuelValue($fuelAmount);
				$this->broadcastFuelAmount($fuelAmount);
			}
		}else{
			$this->setBrewTime(self::MAX_BREW_TIME);
			$this->broadcastBrewTime(0);
		}

		if($return){
			$this->inventory->sendContents($this->inventory->getViewers());
			$this->onChanged();
		}

		$this->timings->stopTiming();

		return $return;
	}

	public function saveNBT(): void{
		$this->saveItems();
	}

	public function loadBottles(): void{
		$this->loadItems();
	}

	public function getBrewTime(): int{
		return $this->namedtag->getInt(self::TAG_BREW_TIME);
	}

	public function setBrewTime(int $time): void{
		$this->namedtag->setInt(self::TAG_BREW_TIME, $time);
	}

	public function getFuelValue(): int{
		return $this->namedtag->getByte(self::TAG_FUEL, 0);
	}

	public function setFuelValue(int $fuel): void{
		$this->namedtag->setByte(self::TAG_FUEL, $fuel);
	}

	public function setBottle(int $slot, bool $hasBottle): void{
		if($slot > -1 && $slot < 3){
			$this->namedtag->setByte(self::TAG_HAS_BOTTLE_BASE . strval($slot), intval($hasBottle));
		}else{
			throw new \InvalidArgumentException("Slot must be in the range of 0-2.");
		}
	}

	public function broadcastBrewTime(int $time): void{
		$pk = new ContainerSetDataPacket();
		$pk->property = ContainerSetDataPacket::PROPERTY_BREWING_STAND_BREW_TIME;
		$pk->value = $time;
		foreach($this->inventory->getViewers() as $viewer){
			$pk->windowId = $viewer->getWindowId($this->getInventory());
			if($pk->windowId > 0){
				$viewer->dataPacket($pk);
			}
		}
	}

	public function broadcastFuelAmount(int $value): void{
		$pk = new ContainerSetDataPacket();
		$pk->property = ContainerSetDataPacket::PROPERTY_BREWING_STAND_FUEL_AMOUNT;
		$pk->value = $value;
		foreach($this->inventory->getViewers() as $viewer){
			$pk->windowId = $viewer->getWindowId($this->getInventory());
			if($pk->windowId > 0){
				$viewer->dataPacket($pk);
			}
		}
	}

	public function broadcastFuelTotal(int $value): void{
		$pk = new ContainerSetDataPacket();
		$pk->property = ContainerSetDataPacket::PROPERTY_BREWING_STAND_FUEL_TOTAL;
		$pk->value = $value;
		foreach($this->inventory->getViewers() as $viewer){
			$pk->windowId = $viewer->getWindowId($this->getInventory());
			if($pk->windowId > 0){
				$viewer->dataPacket($pk);
			}
		}
	}
}