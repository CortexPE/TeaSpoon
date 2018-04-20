<?php

/*
 *               _ _
 *         /\   | | |
 *        /  \  | | |_ __ _ _   _
 *       / /\ \ | | __/ _` | | | |
 *      / ____ \| | || (_| | |_| |
 *     /_/    \_|_|\__\__,_|\__, |
 *                           __/ |
 *                          |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Altay
 *
 * Modified to run as a plugin & follow TeaSpoon's Coding Standards (4/16/2018)
 *
*/

namespace CortexPE\entity\vehicle;

use pocketmine\block\Liquid;
use pocketmine\entity\Vehicle;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Boat extends Vehicle {

	public const TAG_VARIANT = "Variant";

	const NETWORK_ID = self::BOAT;

	public $height = 0.455;
	public $width = 1;
	protected $gravity = 0.9;
	protected $drag = 0.1;


	protected function initEntity(){
		$this->setHealth(4);
		$this->setMaxHealth(4);
		$this->setGenericFlag(self::DATA_FLAG_STACKABLE);
		$this->setImmobile(false);
		$this->setBoatType($this->namedtag->getInt(self::TAG_VARIANT, 0));
		parent::initEntity();
	}

	public function getBoatType(): int{
		return $this->propertyManager->getInt(self::DATA_VARIANT);
	}

	public function setBoatType(int $boatType): void{
		$this->propertyManager->setInt(self::DATA_VARIANT, $boatType);
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->setInt(self::TAG_VARIANT, $this->getBoatType());
	}

	public function getDrops(): array{
		return [
			ItemItem::get(ItemItem::BOAT, $this->getBoatType(), 1),
		];
	}

	public function onUpdate(int $currentTick): bool{
		/*if($this->closed){
			return false;
		}
		$this->onGround = $this->isOnGround() and !$this->isInsideOfWater();
		if($this->getHealth() < $this->getMaxHealth() and $currentTick % 10 == 0){
			$this->heal(new EntityRegainHealthEvent($this, 1, EntityRegainHealthEvent::CAUSE_REGEN));
		}*/

		return parent::onUpdate($currentTick);
	}

	public function attack(EntityDamageEvent $source){
		if($source instanceof EntityDamageByEntityEvent){
			$damager = $source->getDamager();
			if($damager instanceof Player and $damager->isCreative()){
				$source->setDamage($this->getHealth());
			}
		}

		return parent::attack($source);
	}

	public function isOnGround(): bool{
		$block = $this->getLevel()->getBlock($this->floor()->subtract(0, 1)->add(0, $this->getEyeHeight()));

		return ($block instanceof Liquid or $block->isSolid());
	}

	protected function applyGravity(){
		if(!$this->onGround) parent::applyGravity();
	}

	public function onInteract(Player $player, Item $item, int $slot, Vector3 $clickPos): bool{
		//EntityUtils::mountEntity($player, $this);
		return true;
	}
}