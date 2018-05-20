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

namespace CortexPE\entity\object;


use CortexPE\utils\ArmorTypes;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\Player;

class ArmorStand extends Entity {

	protected $gravity = 0.04;
	public $height = 1.975;
	public $width = 0.5;

	// TODO: Poses...
	const NETWORK_ID = self::ARMOR_STAND;

	public const TAG_HAND_ITEMS = "HandItems";
	public const TAG_ARMOR_ITEMS = "ArmorItems";

	/** @var Item */
	protected $itemInHand;
	/** @var Item */
	protected $itemOffHand;
	/** @var Item */
	protected $helmet;
	/** @var Item */
	protected $chestplate;
	/** @var Item */
	protected $leggings;
	/** @var Item */
	protected $boots;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);

		$air = Item::get(Item::AIR)->nbtSerialize();
		if(!$nbt->hasTag(self::TAG_HAND_ITEMS, ListTag::class)){
			$nbt->setTag(new ListTag(self::TAG_HAND_ITEMS, [
				$air, // itemInHand
				$air  // itemOffHand
			], NBT::TAG_Compound));
		}

		if(!$nbt->hasTag(self::TAG_ARMOR_ITEMS, ListTag::class)){
			$nbt->setTag(new ListTag(self::TAG_ARMOR_ITEMS, [
				$air, // boots
				$air, // leggings
				$air, // chestplate
				$air  // helmet
			], NBT::TAG_Compound));
		}

		$handItems = $nbt->getListTag(self::TAG_HAND_ITEMS);
		$armorItems = $nbt->getListTag(self::TAG_ARMOR_ITEMS);

		$this->itemInHand = Item::nbtDeserialize($handItems[0]);
		$this->itemOffHand = Item::nbtDeserialize($handItems[1]);

		$this->helmet = Item::nbtDeserialize($armorItems[3]);
		$this->chestplate = Item::nbtDeserialize($armorItems[2]);
		$this->leggings = Item::nbtDeserialize($armorItems[1]);
		$this->boots = Item::nbtDeserialize($armorItems[0]);

		$this->setHealth(6);
		$this->setMaxHealth(6);
	}

	public function canCollideWith(Entity $entity): bool{
		return false;
	}

	public function onInteract(Player $player, Item $item, int $slot, Vector3 $clickPos): bool{
		if(!$player->isSneaking()){
			$diff = $clickPos->getY() - $this->getY();
			$type = ArmorTypes::getType($item);
			$playerInv = $player->getInventory();

			switch(true){ // yes order matter here.
				case ($diff < 0.5):
					$clicked = ArmorTypes::TYPE_BOOTS;
					break;
				case ($diff < 1):
					$clicked = ArmorTypes::TYPE_LEGGINGS;
					break;
				case ($diff < 1.5):
					$clicked = ArmorTypes::TYPE_CHESTPLATE;
					break;
				default: // armor stands are only 2-ish blocks tall :shrug:
					$clicked = ArmorTypes::TYPE_HELMET;
					break;
			}

			if($item->isNull()){
				if($clicked == ArmorTypes::TYPE_CHESTPLATE){
					if($this->getItemInHand()->isNull()){
						$ASchestplate = clone $this->getChestplate();
						$this->setChestplate($item);
						$playerInv->setItemInHand(Item::get(Item::AIR));
						$playerInv->addItem($ASchestplate);
					} else {
						$ASiteminhand = clone $this->getItemInHand();
						$this->setItemInHand($item);
						$playerInv->setItemInHand(Item::get(Item::AIR));
						$playerInv->addItem($ASiteminhand);
					}
				} else {
					$old = clone $this->get($clicked);
					$this->set($clicked, $item);
					$playerInv->setItemInHand(Item::get(Item::AIR));
					$playerInv->addItem($old);
				}
			} else {
				if($type == ArmorTypes::TYPE_NULL){
					if($this->getItemInHand()->equals($item)){
						$playerInv->addItem(clone $this->getItemInHand());
						$this->setItemInHand(Item::get(Item::AIR));
					} else {
						$playerInv->addItem(clone $this->getItemInHand());

						$ic = clone $item;
						$ic->count--;
						$this->setItemInHand((clone $ic)->setCount(1));
						$playerInv->setItemInHand($ic);
					}
				} else {
					$old = clone $this->get($type);
					$this->set($type, $item);
					$playerInv->setItemInHand(Item::get(Item::AIR));
					$playerInv->addItem($old);
				}
			}

			//$playerInv->sendContents($playerInv->getViewers()); WTF why doesn't it work when these are included?
			$this->sendAll();
		}
		return true;
	}

	private function get(string $armorType) : Item { // pure laziness xD
		switch($armorType){
			case ArmorTypes::TYPE_HELMET:
				return $this->getHelmet();
			case ArmorTypes::TYPE_CHESTPLATE:
				return $this->getChestplate();
			case ArmorTypes::TYPE_LEGGINGS:
				return $this->getLeggings();
			case ArmorTypes::TYPE_BOOTS:
				return $this->getBoots();
			case "INHAND":
				return $this->getItemInHand();
			case "OFFHAND":
				return $this->getItemOffHand();
		}
		return Item::get(Item::AIR);
	}

	private function set(string $armorType, Item $item){ // pure laziness aswell xD
		switch($armorType){
			case ArmorTypes::TYPE_HELMET:
				$this->setHelmet($item);
				break;
			case ArmorTypes::TYPE_CHESTPLATE:
				$this->setChestplate($item);
				break;
			case ArmorTypes::TYPE_LEGGINGS:
				$this->setLeggings($item);
				break;
			case ArmorTypes::TYPE_BOOTS:
				$this->setBoots($item);
				break;
			case "INHAND":
				$this->setItemInHand($item);
				break;
			case "OFFHAND":
				$this->setItemOffHand($item);
				break;
		}
	}

	public function kill(): void{
		$this->level->dropItem($this, Item::get(Item::ARMOR_STAND));
		$this->level->dropItem($this, $this->getItemInHand());
		$this->level->dropItem($this, $this->getItemOffHand());
		$this->level->dropItem($this, $this->getHelmet());
		$this->level->dropItem($this, $this->getChestplate());
		$this->level->dropItem($this, $this->getLeggings());
		$this->level->dropItem($this, $this->getBoots());
		parent::kill();
	}

	public function spawnTo(Player $player): void{
		parent::spawnTo($player);
		$this->sendArmorItems($player);
		$this->sendHandItems($player);
	}

	public function sendHandItems(Player $player){
		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->inventorySlot = $pk->hotbarSlot = 0;
		$pk->item = $this->getItemInHand();
		$player->dataPacket($pk);

		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->inventorySlot = $pk->hotbarSlot = 1;
		$pk->item = $this->getItemOffHand();
		$player->dataPacket($pk);
	}

	public function sendArmorItems(Player $player){
		$pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->slots = [$this->getHelmet(), $this->getChestplate(), $this->getLeggings(), $this->getBoots()];
		$player->dataPacket($pk);
	}

	public function sendAll(){
		foreach($this->getViewers() as $player){
			$this->sendHandItems($player);
			$this->sendArmorItems($player);
		}
	}

	public function saveNBT(): void{
		parent::saveNBT();
		$this->namedtag->setTag(new ListTag(self::TAG_ARMOR_ITEMS, [
			$this->boots->nbtSerialize(),
			$this->leggings->nbtSerialize(),
			$this->chestplate->nbtSerialize(),
			$this->helmet->nbtSerialize(),
		], NBT::TAG_Compound));
		$this->namedtag->setTag(new ListTag(self::TAG_HAND_ITEMS, [
			$this->getItemInHand()->nbtSerialize(),
			$this->getItemOffHand()->nbtSerialize(),
		], NBT::TAG_Compound));
	}

	public function getItemInHand(): Item{
		return $this->itemInHand;
	}

	public function setItemInHand(Item $item){
		$this->itemInHand = $item;
		$this->sendAll();
	}

	public function getItemOffHand(): Item{
		return $this->itemOffHand;
	}

	public function setItemOffHand(Item $item){
		$this->itemOffHand = $item;
		$this->sendAll();
	}

	public function getHelmet(): Item{
		return $this->helmet;
	}

	public function setHelmet(Item $item){
		$this->helmet = $item;
		$this->sendAll();
	}

	public function getChestplate(): Item{
		return $this->chestplate;
	}

	public function setChestplate(Item $item){
		$this->chestplate = $item;
		$this->sendAll();
	}

	public function getLeggings(): Item{
		return $this->leggings;
	}

	public function setLeggings(Item $item){
		$this->leggings = $item;
		$this->sendAll();
	}

	public function getBoots(): Item{
		return $this->boots;
	}

	public function setBoots(Item $item){
		$this->boots = $item;
		$this->sendAll();
	}

	// A E S T H E T I C S  --  from Altay
	public function applyGravity(): void{
		$this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_SOUND_ARMOR_STAND_FALL);
		parent::applyGravity();
	}

	public function attack(EntityDamageEvent $source): void{
		if($source instanceof EntityDamageByEntityEvent){
			$damager = $source->getDamager();
			if($damager instanceof Player){
				if($damager->isCreative()){
					$this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_SOUND_ARMOR_STAND_BREAK);
					$this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_PARTICLE_DESTROY, 5);
					$this->flagForDespawn();
				}else{
					$this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_SOUND_ARMOR_STAND_HIT);
				}
			}
		}
		if($source->getCause() != EntityDamageEvent::CAUSE_CONTACT){ // cactus
			Entity::attack($source);
		}
	}
	// A E S T H E T I C S  --  from Altay
}
