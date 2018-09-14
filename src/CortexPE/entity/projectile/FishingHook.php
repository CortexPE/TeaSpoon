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

namespace CortexPE\entity\projectile;


use pocketmine\block\StillWater;
use pocketmine\block\Water;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\math\RayTraceResult;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\Player;
use pocketmine\Server as PMServer;

class FishingHook extends Projectile {

	public const NETWORK_ID = self::FISHING_HOOK;

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;
	public $coughtTimer = 0;
	public $attractTimer = 0;
	protected $gravity = 0.1;
	protected $drag = 0.05;
	protected $touchedWater = false;

	public function onUpdate(int $currentTick): bool{
		if($this->isFlaggedForDespawn() || !$this->isAlive()){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::onUpdate($currentTick);

		if($this->isCollidedVertically){
			$this->motion->x = 0;
			$this->motion->y += 0.01;
			$this->motion->z = 0;
			$hasUpdate = true;
		}elseif($this->isCollided && $this->keepMovement === true){
			$this->motion->x = 0;
			$this->motion->y = 0;
			$this->motion->z = 0;
			$this->keepMovement = false;
			$hasUpdate = true;
		}
		if($this->isCollided && !$this->touchedWater){
			foreach($this->getBlocksAround() as $block){
				if($block instanceof Water || $block instanceof StillWater){
					$this->touchedWater = true;

					$pk = new EntityEventPacket();
					$pk->entityRuntimeId = $this->getId();
					$pk->event = EntityEventPacket::FISH_HOOK_POSITION;
					PMServer::getInstance()->broadcastPacket($this->getViewers(), $pk);

					break;
				}
			}
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

	public function attractFish(){
		$oe = $this->getOwningEntity();
		if($oe instanceof Player){
			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->getId();
			$pk->event = EntityEventPacket::FISH_HOOK_BUBBLE;
			PMServer::getInstance()->broadcastPacket($this->getViewers(), $pk);
		}
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

	public function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
		$this->server->getPluginManager()->callEvent(new ProjectileHitEntityEvent($this, $hitResult, $entityHit));

		$damage = $this->getResultDamage();

		if($this->getOwningEntity() === null){
			$ev = new EntityDamageByEntityEvent($this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
		}else{
			$ev = new EntityDamageByChildEntityEvent($this->getOwningEntity(), $this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
		}

		$entityHit->attack($ev);

		$entityHit->setMotion($this->getOwningEntity()->getDirectionVector()->multiply(-0.3)->add(0, 0.3, 0));

		$this->isCollided = true;
		$this->flagForDespawn();
	}

	public function getResultDamage(): int{
		return 1;
	}
}
