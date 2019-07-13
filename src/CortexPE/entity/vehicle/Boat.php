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

namespace CortexPE\entity\vehicle;

use pocketmine\entity\Entity;
use pocketmine\entity\Vehicle;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\nbt\tag\{
	ByteTag
};
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\Server as PMServer;

class Boat extends Vehicle {

	public const TAG_WOOD_ID = "WoodID";

	public const NETWORK_ID = self::BOAT;

	public $height = 0.7;
	public $width = 1.6;
	public $gravity = 0;
	public $drag = 0.1;

	/** @var Entity */
	public $linkedEntity = null;
	protected $age = 0;

	public function initEntity(): void{
		if(!$this->namedtag->hasTag(self::TAG_WOOD_ID, ByteTag::class)){
			$this->namedtag->setByte(self::TAG_WOOD_ID, 0);
		}
		$this->setMaxHealth(4);

		parent::initEntity();
	}

	public function getDrops(): array{
		return [
			ItemItem::get(ItemItem::BOAT, $this->getWoodID(), 1),
		];
	}

	public function getWoodID(){
		return $this->namedtag->getByte(self::TAG_WOOD_ID);
	}

	public function attack(EntityDamageEvent $source): void{
		parent::attack($source);
		if(!$source->isCancelled()){
			$pk = new ActorEventPacket();
			$pk->entityRuntimeId = $this->id;
			$pk->event = ActorEventPacket::HURT_ANIMATION;
			PMServer::getInstance()->broadcastPacket($this->getViewers(), $pk);
		}
	}

	public function entityBaseTick(int $tickDiff = 1): bool{
		return false;/* TODO
		if($this->closed){
			return false;
		}
		if($tickDiff <= 0 and !$this->justCreated){
			return true;
		}
		$this->lastUpdate = PMServer::getInstance()->getTick();
		$this->timings->startTiming();
		$hasUpdate = parent::entityBaseTick($tickDiff);
		if(!$this->level->getBlock(new Vector3($this->x, $this->y, $this->z))->getBoundingBox() == null or $this->isInsideOfWater()){
			$this->motionY = 0.1;
		}else{
			$this->motionY = -0.08;
		}
		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->updateMovement();
		if(!($this->linkedEntity instanceof Entity)){
			if($this->age > 1500){
				$this->flagForDespawn();
				$hasUpdate = true;
				//$this->scheduleUpdate();
				$this->age = 0;
			}
			$this->age++;
		}else $this->age = 0;
		$this->timings->stopTiming();

		return $hasUpdate or !$this->onGround or abs($this->motionX) > 0.00001 or abs($this->motionY) > 0.00001 or abs($this->motionZ) > 0.00001;
	*/
	}
}
