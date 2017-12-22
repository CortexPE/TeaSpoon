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

namespace CortexPE\entity\projectile;

use CortexPE\Main;
use CortexPE\utils\Xp;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\Player;
use pocketmine\Server as PMServer;

class FishingHook extends Projectile {
	const NETWORK_ID = self::FISHING_HOOK;
	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;
	protected $gravity = 0.1;
	protected $drag = 0.05;

	public $coughtTimer = 0;
	public $attractTimer = 0;

	public function onUpdate(int $currentTick) : bool {
		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::onUpdate($currentTick);

		if($this->isCollidedVertically){
			$this->motionX = 0;
			$this->motionY += 0.01;
			$this->motionZ = 0;
			$hasUpdate = true;
		}elseif($this->isCollided && $this->keepMovement === true){
			$this->motionX = 0;
			$this->motionY = 0;
			$this->motionZ = 0;
			$this->keepMovement = false;
			$hasUpdate = true;
		}
		if($this->hadCollision){
			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->getId();
			$pk->event = EntityEventPacket::FISH_HOOK_POSITION;
			PMServer::getInstance()->broadcastPacket($this->getLevel()->getPlayers(), $pk);
		}

		if($this->attractTimer === 0 && mt_rand(0, 100) <= 30){
			$this->coughtTimer = mt_rand(5, 10) * 20;
			$this->attractTimer = mt_rand(30, 100) * 20;
			$this->attractFish();
			$oe = $this->getOwningEntity();
			if($oe instanceof Player){
				$oe->sendTip("A fish bites!");
			}
		}elseif($this->attractTimer > 0){
			$this->attractTimer--;
		}
		if($this->coughtTimer > 0){
			$this->coughtTimer--;
			$this->fishBites();
		}

		$this->timings->stopTiming();

		return $hasUpdate;
	}

	public function fishBites(){
		$oe = $this->getOwningEntity();
		if($oe instanceof Player){
			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->getId();
			$pk->event = EntityEventPacket::FISH_HOOK_HOOK;
			PMServer::getInstance()->broadcastPacket($this->getViewers(), $pk);
		}
	}

	public function attractFish(){
		$oe = $this->getOwningEntity();
		if($oe instanceof Player){
			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->getId();
			$pk->event = EntityEventPacket::FISH_HOOK_BUBBLE;
			PMServer::getInstance()->broadcastPacket($this->getViewers(), $pk);
		}
	}

	public function onCollideWithEntity(Entity $entity){
		$this->server->getPluginManager()->callEvent(new ProjectileHitEvent($this));

		$damage = $this->getResultDamage();

		if($this->getOwningEntity() === null){
			$ev = new EntityDamageByEntityEvent($this, $entity, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
		}else{
			$ev = new EntityDamageByChildEntityEvent($this->getOwningEntity(), $this, $entity, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
		}

		$entity->attack($ev);

		$entity->setMotion($this->getOwningEntity()->getDirectionVector()->multiply(-0.3)->add(0, 0.3, 0));

		$this->hadCollision = true;
		$this->flagForDespawn();
	}

	public function getResultDamage(): int{
		return 1;
	}
}
