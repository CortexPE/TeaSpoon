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

declare(strict_types = 1);

namespace CortexPE\tile;

use CortexPE\inventory\BeaconInventory;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\Server as PMServer;
use pocketmine\tile\Spawnable;

class Beacon extends Spawnable implements InventoryHolder {
	/**
	 * @var BeaconInventory
	 */
	private $inventory;

	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->primary)){
			$nbt->primary = new IntTag("primary", 0);
		}
		if(!isset($nbt->secondary)){
			$nbt->secondary = new IntTag("secondary", 0);
		}
		$this->inventory = new BeaconInventory($this);
		parent::__construct($level, $nbt);
		$this->scheduleUpdate();
	}

	public function saveNBT(): void{
		parent::saveNBT();
	}

	public function addAdditionalSpawnData(CompoundTag $nbt): void{
		$nbt->primary = $this->namedtag->primary;
		$nbt->secondary = $this->namedtag->secondary;
		//TODO: isMovable
	}

	public function updateCompoundTag(CompoundTag $nbt, Player $player): bool{
		$this->setPrimaryEffect($nbt->primary->getValue());
		$this->setSecondaryEffect($nbt->secondary->getValue());

		return true;
	}

	public function setPrimaryEffect(int $effectId){
		$this->namedtag->primary->setValue($effectId);
	}

	public function setSecondaryEffect(int $effectId){
		$this->namedtag->secondary->setValue($effectId);
	}

	public function isPaymentItem(Item $item){//TODO: When FloatingInv implemented, remove item
		return in_array($item->getId(), [Item::DIAMOND, Item::IRON_INGOT, Item::GOLD_INGOT, Item::EMERALD]);
	}

	public function getPrimaryEffect(){
		return $this->namedtag->primary->getValue();
	}

	public function getBeaconData(){
		return $this->namedtag;
	}

	public function isSecondaryAvailable(){
		return $this->isEffectAvailable(Effect::REGENERATION);//What a hack xD
	}

	public function isEffectAvailable(int $effectId){
		switch($effectId){
			case Effect::SPEED:
			case Effect::HASTE:
				return $this->getLayers() >= 1 && !$this->solidAbove();
				break;
			case Effect::DAMAGE_RESISTANCE:
			case Effect::JUMP:
				return $this->getLayers() >= 2 && !$this->solidAbove();
				break;
			case Effect::STRENGTH:
				return $this->getLayers() >= 3 && !$this->solidAbove();
				break;
			case Effect::REGENERATION:
				//this case is for secondary effect only
				return $this->getLayers() >= 4 && !$this->solidAbove();
				break;
			default:
				return false;
		}
	}

	public function getLayers(){
		$layers = 0;
		if($this->checkShape($this->getSide(0), 1)) $layers++;
		else
			return $layers;
		if($this->checkShape($this->getSide(0, 2), 2)) $layers++;
		else
			return $layers;
		if($this->checkShape($this->getSide(0, 3), 3)) $layers++;
		else
			return $layers;
		if($this->checkShape($this->getSide(0, 4), 4)) $layers++;

		return $layers;
	}

	public function checkShape(Vector3 $pos, $layer = 1){
		for($x = $pos->x - $layer; $x <= $pos->x + $layer; $x++)
			for($z = $pos->z - $layer; $z <= $pos->z + $layer; $z++)
				if(!in_array($this->getLevel()->getBlockIdAt($x, $pos->y, $z), [Block::DIAMOND_BLOCK, Block::IRON_BLOCK, Block::EMERALD_BLOCK, Block::GOLD_BLOCK])) return false;

		return true;
	}

	public function solidAbove(){
		if($this->y === $this->getLevel()->getHighestBlockAt($this->x, $this->z)) return false;
		for($i = $this->y; $i < Level::Y_MAX; $i++){
			if(($block = $this->getLevel()->getBlock(new Vector3($this->x, $i, $this->z)))->isSolid() && !$block->getId() === Block::BEACON) return true;
		}

		return false;
	}

	public function isActive(){
		return !empty($this->getEffects()) && $this->checkShape($this->getSide(0), 1);
	}

	public function getEffects(){
		return [$this->namedtag->primary->getValue(), $this->namedtag->secondary->getValue()];
	}

	public function getTierEffects(){
	}

	public function getEffectTier(int $tier){
	}

	public function onUpdate(): bool{
		if(PMServer::getInstance()->getTick() % (20 * 4)){
			if($this->getLevel() instanceof Level){
				if(!PMServer::getInstance()->isLevelLoaded($this->getLevel()->getName()) || !$this->getLevel()->isChunkLoaded($this->x >> 4, $this->z >> 4)) return false;
				if(!empty($this->getEffects())){
					$this->applyEffects($this);
				}
			}
		}

		return true;
	}

	public function applyEffects(Vector3 $pos){
		//TODO: Apply stronger effects on secondary.
		$layers = $this->getLayers();
		/** @var Player $player */
		foreach($this->getLevel()->getCollidingEntities(new AxisAlignedBB($pos->x - (10 + 10 * $layers), 0, $pos->z - (10 + 10 * $layers), $pos->x + (10 + 10 * $layers), Level::Y_MAX, $pos->z + (10 + 10 * $layers))) as $player)
			foreach($this->getEffects() as $effectId){
				if($this->isEffectAvailable($effectId) and $player instanceof Player){
					$player->removeEffect($effectId);//Pretty hacky..
					$effect = Effect::getEffect($effectId)->setDuration(20 * 9 + $layers * 2 * 20);
					if($this->getSecondaryEffect() !== 0 && $this->getSecondaryEffect() !== Effect::REGENERATION)
						$effect->setAmplifier(1);
					$player->addEffect($effect);
				}
			}
	}

	public function getSecondaryEffect(){
		return $this->namedtag->secondary->getValue();
	}

	/**
	 * Get the object related inventory
	 *
	 * @return BeaconInventory
	 */
	public function getInventory(){
		return $this->inventory;
	}
}
