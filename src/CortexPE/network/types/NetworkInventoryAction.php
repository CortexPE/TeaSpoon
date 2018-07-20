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

namespace CortexPE\network\types;

use CortexPE\inventory\AnvilInventory;
use CortexPE\inventory\EnchantInventory;
use CortexPE\Main;
use CortexPE\network\InventoryTransactionPacket;
use pocketmine\inventory\transaction\action\CreativeInventoryAction;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class NetworkInventoryAction {

    /** @var int */
	public const
        SOURCE_CONTAINER = 0,
        SOURCE_WORLD = 2, //drop/pickup item entity
        SOURCE_CREATIVE = 3,
        SOURCE_TODO = 99999;

	/**
	 * Fake window IDs for the SOURCE_TODO type (99999)
	 *
	 * These identifiers are used for inventory source types which are not currently implemented server-side in MCPE.
	 * As a general rule of thumb, anything that doesn't have a permanent inventory is client-side. These types are
	 * to allow servers to track what is going on in client-side windows.
	 *
	 * Expect these to change in the future.
	 */
	public const
        SOURCE_TYPE_CRAFTING_ADD_INGREDIENT = -2,
        SOURCE_TYPE_CRAFTING_REMOVE_INGREDIENT = -3,
        SOURCE_TYPE_CRAFTING_RESULT = -4,
        SOURCE_TYPE_CRAFTING_USE_INGREDIENT = -5,

	    SOURCE_TYPE_ANVIL_INPUT = -10,
        OURCE_TYPE_ANVIL_MATERIAL = -11,
        SOURCE_TYPE_ANVIL_RESULT = -12,
        SOURCE_TYPE_ANVIL_OUTPUT = -13,

	    SOURCE_TYPE_ENCHANT_INPUT = -15,
        SOURCE_TYPE_ENCHANT_MATERIAL = -16,
        SOURCE_TYPE_ENCHANT_OUTPUT = -17,

        SOURCE_TYPE_TRADING_INPUT_1 = -20,
        SOURCE_TYPE_TRADING_INPUT_2 = -21,
        SOURCE_TYPE_TRADING_USE_INPUTS = -22,
        SOURCE_TYPE_TRADING_OUTPUT = -23,

        SOURCE_TYPE_BEACON = -24,

	/** Any client-side window dropping its contents when the player closes it */
	    SOURCE_TYPE_CONTAINER_DROP_CONTENTS = -100;

	/** @var int */
	public const
        ACTION_MAGIC_SLOT_CREATIVE_DELETE_ITEM = 0,
        ACTION_MAGIC_SLOT_CREATIVE_CREATE_ITEM = 1;

	/** @var int */
	public const
        ACTION_MAGIC_SLOT_DROP_ITEM = 0,
        ACTION_MAGIC_SLOT_PICKUP_ITEM = 1;

	/** @var int */
	public $sourceType;
	/** @var int */
	public $windowId = ContainerIds::NONE;
	/** @var int */
	public $sourceFlags = 0;
	/** @var int */
	public $inventorySlot;
	/** @var Item */
	public $oldItem;
	/** @var Item */
	public $newItem;

	/**
	 * @param InventoryTransactionPacket $packet
	 * @return $this
	 */
	public function read(InventoryTransactionPacket $packet){
		$this->sourceType = $packet->getUnsignedVarInt();

		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				$this->windowId = $packet->getVarInt();
				break;
			case self::SOURCE_WORLD:
				$this->sourceFlags = $packet->getUnsignedVarInt();
				break;
			case self::SOURCE_CREATIVE:
				break;
			case self::SOURCE_TODO:
				$this->windowId = $packet->getVarInt();
				switch($this->windowId){
					/** @noinspection PhpMissingBreakStatementInspection */
					case self::SOURCE_TYPE_CRAFTING_RESULT:
						$packet->isFinalCraftingPart = true;
					case self::SOURCE_TYPE_CRAFTING_USE_INGREDIENT:
						$packet->isCraftingPart = true;
						break;
				}
				break;
		}

		$this->inventorySlot = $packet->getUnsignedVarInt();
		$this->oldItem = $packet->getSlot();
		$this->newItem = $packet->getSlot();

		return $this;
	}

	/**
	 * @param InventoryTransactionPacket $packet
	 */
	public function write(InventoryTransactionPacket $packet){
		$packet->putUnsignedVarInt($this->sourceType);

		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				$packet->putVarInt($this->windowId);
				break;
			case self::SOURCE_WORLD:
				$packet->putUnsignedVarInt($this->sourceFlags);
				break;
			case self::SOURCE_CREATIVE:
				break;
			case self::SOURCE_TODO:
				$packet->putVarInt($this->windowId);
				break;
		}

		$packet->putUnsignedVarInt($this->inventorySlot);
		$packet->putSlot($this->oldItem);
		$packet->putSlot($this->newItem);
	}

	/**
	 * @param Player $player
	 *
	 * @return InventoryAction|null
	 */
	public function createInventoryAction(Player $player){
		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				$window = $player->getWindow($this->windowId);
				if($window !== null){
					return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem, $this->newItem);
				}

				throw new \InvalidStateException("Player " . $player->getName() . " has no open container with window ID $this->windowId");
			case self::SOURCE_WORLD:
				if($this->inventorySlot !== self::ACTION_MAGIC_SLOT_DROP_ITEM){
					throw new \UnexpectedValueException("Only expecting drop-item world actions from the client!");
				}

				return new DropItemAction($this->newItem);
			case self::SOURCE_CREATIVE:
				switch($this->inventorySlot){
					case self::ACTION_MAGIC_SLOT_CREATIVE_DELETE_ITEM:
						$type = CreativeInventoryAction::TYPE_DELETE_ITEM;
						break;
					case self::ACTION_MAGIC_SLOT_CREATIVE_CREATE_ITEM:
						$type = CreativeInventoryAction::TYPE_CREATE_ITEM;
						break;
					default:
						throw new \UnexpectedValueException("Unexpected creative action type $this->inventorySlot");

				}

				return new CreativeInventoryAction($this->oldItem, $this->newItem, $type);
			case self::SOURCE_TODO:
				//These types need special handling.
				switch($this->windowId){
					case self::SOURCE_TYPE_CRAFTING_ADD_INGREDIENT:
					case self::SOURCE_TYPE_CRAFTING_REMOVE_INGREDIENT:
						$window = $player->getCraftingGrid();
						return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem, $this->newItem);
					case self::SOURCE_TYPE_CRAFTING_RESULT:
					case self::SOURCE_TYPE_CRAFTING_USE_INGREDIENT:
						return null;

					case self::SOURCE_TYPE_CONTAINER_DROP_CONTENTS:
						//TODO: this type applies to all fake windows, not just crafting
						$window = $player->getCraftingGrid();

						//DROP_CONTENTS doesn't bother telling us what slot the item is in, so we find it ourselves
						$inventorySlot = $window->first($this->oldItem, true);
						if($inventorySlot === -1){
							throw new \InvalidStateException("Fake container " . get_class($window) . " for " . $player->getName() . " does not contain $this->oldItem");
						}
						return new SlotChangeAction($window, $inventorySlot, $this->oldItem, $this->newItem);

					case self::SOURCE_TYPE_ENCHANT_INPUT:
					case self::SOURCE_TYPE_ENCHANT_MATERIAL:
					case self::SOURCE_TYPE_ENCHANT_OUTPUT:
						$inv = $player->getWindow(WindowTypes::ENCHANTMENT);
						if(!($inv instanceof EnchantInventory)){
							Main::getInstance()->getLogger()->debug("Player " . $player->getName() . " has no open enchant container");

							return null;
						}
						switch($this->windowId){
							case self::SOURCE_TYPE_ENCHANT_INPUT:
								$this->inventorySlot = 0;
								$local = $inv->getItem(0);
								if($local->equals($this->newItem, true, false)){
									$inv->setItem(0, $this->newItem);
								}
								break;
							case self::SOURCE_TYPE_ENCHANT_MATERIAL:
								$this->inventorySlot = 1;
								break;
							case self::SOURCE_TYPE_ENCHANT_OUTPUT:
								$inv->sendSlot(0, $player);
								return null;
						}

						return new SlotChangeAction($inv, $this->inventorySlot, $this->oldItem, $this->newItem);

					case self::SOURCE_TYPE_ANVIL_INPUT:
					case self::SOURCE_TYPE_ANVIL_MATERIAL:
					case self::SOURCE_TYPE_ANVIL_RESULT:
					case self::SOURCE_TYPE_ANVIL_OUTPUT:
						$inv = $player->getWindow(WindowTypes::ANVIL);
						if(!($inv instanceof AnvilInventory)){
							Main::getInstance()->getLogger()->debug("Player " . $player->getName() . " has no open anvil container");

							return null;
						}
						switch($this->windowId){
							case self::SOURCE_TYPE_ANVIL_INPUT:
								$this->inventorySlot = 0;
								return new SlotChangeAction($inv, $this->inventorySlot, $this->oldItem, $this->newItem);
								break;
							case self::SOURCE_TYPE_ANVIL_MATERIAL:
								$this->inventorySlot = 1;
								return new SlotChangeAction($inv, $this->inventorySlot, $this->oldItem, $this->newItem);
								break;
							case self::SOURCE_TYPE_ANVIL_OUTPUT:
								break;
							case self::SOURCE_TYPE_ANVIL_RESULT:
								$this->inventorySlot = 2;
								$cost = $this->oldItem->getNamedTag()->getInt("RepairCost", 1);
								if($player->getXpLevel() < $cost){
									break;
								}
								$inv->clear(0);
								$inv->clear(1);
								$inv->setItem(2, $this->oldItem);
								if($player->isSurvival()){
									$player->subtractXpLevels($cost);
								}
								return new SlotChangeAction($inv, $this->inventorySlot, $this->oldItem, $this->newItem);
								break;
						}
				}

				//TODO: more stuff
				throw new \UnexpectedValueException("Player " . $player->getName() . " has no open container with window ID $this->windowId");
			default:
				throw new \UnexpectedValueException("Unknown inventory source type $this->sourceType");
		}
	}

}