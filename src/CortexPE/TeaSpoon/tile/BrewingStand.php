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
 * @author CortexPE, and contributors
 * @link   https://github.com/CortexPE/TeaSpoon
 *
 */
declare(strict_types=1);

namespace CortexPE\TeaSpoon\tile;


use CortexPE\TeaSpoon\inventory\BrewingInventory;
use CortexPE\TeaSpoon\module\impl\Brewing;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\tile\Container;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Nameable;
use pocketmine\tile\NameableTrait;
use pocketmine\tile\Spawnable;
use function in_array;
use function max;

class BrewingStand extends Spawnable implements InventoryHolder, Container, Nameable {
	use NameableTrait, ContainerTrait;

	/** @var string */
	public const TAG_BREW_TIME = "BrewTime";
	public const TAG_FUEL = "Fuel";
	public const TAG_HAS_BOTTLE_0 = "has_bottle_0";
	public const TAG_HAS_BOTTLE_1 = "has_bottle_1";
	public const TAG_HAS_BOTTLE_2 = "has_bottle_2";
	protected const TAG_HAS_BOTTLE_BASE = "has_bottle_";

	/** @var int */
	public const MAX_BREW_TIME = 400;
	public const MAX_FUEL = 20;

	/** @var BrewingInventory */
	private $inventory;

	/** @var int */
	protected $fuel = 0;
	/** @var int */
	protected $brewTime = self::MAX_BREW_TIME;
	/** @var bool[] */
	protected $hasBottle = [];

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
		$this->scheduleUpdate();
	}

	public function getRealInventory() {
		return $this->inventory;
	}

	public function getInventory() {
		return $this->inventory;
	}

	public function getDefaultName(): string {
		return "Brewing Stand";
	}

	protected function readSaveData(CompoundTag $nbt): void {
		$this->loadName($nbt);

		$this->inventory = new BrewingInventory($this);
		$this->loadItems($nbt);

		$this->brewTime = $nbt->getInt(self::TAG_BREW_TIME, 0);
		$this->fuel = $nbt->getByte(self::TAG_FUEL, 0);
		for($i = 0; $i < 3; $i++) {
			$this->hasBottle[$i] = (bool)$nbt->getByte(self::TAG_HAS_BOTTLE_BASE . $i, 0);
		}
	}

	protected function writeSaveData(CompoundTag $nbt): void {
		$this->saveName($nbt);
		$this->saveItems($nbt);
		$nbt->setInt(self::TAG_BREW_TIME, $this->brewTime);
		$nbt->setByte(self::TAG_FUEL, $this->fuel);
		for($i = 0; $i < 3; $i++) {
			$nbt->setByte(self::TAG_HAS_BOTTLE_BASE . $i, (int)$this->hasBottle[$i]);
		}
	}

	public function onUpdate(): bool {
		if($this->isClosed()) {
			return false;
		}

		$this->timings->startTiming();

		$this->broadcastFuelStatus(); // ree... client-side visual bug
		if(
			($this->fuel <= 0 && !$this->hasValidFuel()) || // can't brew, either no fuel and no stocked valid fuel
			!$this->hasValidBottle() || // can't brew, no potion bottles to brew with
			!$this->hasValidRecipe() // can't brew, no valid recipe
		) {
			$this->brewTime = self::MAX_BREW_TIME;

			return true;
		}

		if($this->fuel <= 0) {
			// replenish fuel if needed
			$fuel = $this->getInventory()->getFuel();
			$fuel->pop();
			$this->inventory->setFuel($fuel);
			$this->fuel = self::MAX_FUEL;
		}

		$this->brewTime--;

		if($this->brewTime <= 0) {
			$ingredient = $this->inventory->getIngredient();
			for($i = 0; $i < 3; $i++) {
				$this->hasBottle[$i] = false;

				$slot = $i + 1; // offset by one cuz first slot = fuel
				$potion = $this->inventory->getItem($slot);
				if(!$potion->isNull() && ($result = Brewing::resolveResult($ingredient, $potion)) !== null) {
					$this->inventory->setItem($slot, $result);
					$this->hasBottle[$i] = true;
				}
			}
			$this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_POTION_BREWED);
			$ingredient->pop();
			$this->inventory->setIngredient($ingredient);

			$this->fuel = max($this->fuel - 1, 0); // decrease fuel, keep it at range of 0+
		}

		$this->onChanged();

		$this->timings->stopTiming();

		return true;
	}

	protected function onChanged(): void {
		parent::onChanged();
		$this->broadcastProperty(ContainerSetDataPacket::PROPERTY_BREWING_STAND_BREW_TIME, $this->brewTime);
	}

	protected function broadcastFuelStatus():void {
		$this->broadcastProperty(ContainerSetDataPacket::PROPERTY_BREWING_STAND_FUEL_AMOUNT, $this->fuel);
		$this->broadcastProperty(ContainerSetDataPacket::PROPERTY_BREWING_STAND_FUEL_TOTAL, self::MAX_FUEL);
	}

	private function broadcastProperty(int $property, int $value): void {
		$pk = new ContainerSetDataPacket();
		$pk->property = $property;
		$pk->value = $value;
		foreach($this->inventory->getViewers() as $viewer) {
			$pk->windowId = $viewer->getWindowId($this->getInventory());
			if($pk->windowId > 0) {
				$viewer->dataPacket($pk);
			}
		}
	}

	public static function isValidPotion(Item $item): bool {
		return in_array($item->getId(), [Item::POTION, Item::SPLASH_POTION]);
	}

	public function hasValidFuel(): bool {
		return $this->getInventory()->getFuel()->getId() === Item::BLAZE_POWDER;
	}

	public function hasValidBottle(): bool {
		foreach($this->inventory->getPotions() as $potion) {
			if($this->isValidPotion($potion)) {
				return true;
			}
		}

		return false;
	}

	public function hasValidRecipe(): bool {
		$ingredient = clone $this->getInventory()->getIngredient();
		if($ingredient->isNull()) {
			return false;
		}
		foreach($this->inventory->getPotions() as $potion) {
			if(Brewing::resolveResult($ingredient, $potion) !== null) {
				return true;
			}
		}

		return false;
	}
}