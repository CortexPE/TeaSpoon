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

use CortexPE\Main;
use pocketmine\entity\Human;
use pocketmine\entity\object\ExperienceOrb as PMXPOrb;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

class ExperienceOrb extends PMXPOrb {
	/** @var Human */
	protected $targetPlayer = null;

	public function hasTargetPlayer() : bool{
		return $this->targetPlayer instanceof Human;
	}

	public function getTargetPlayer() : ?Human{
		return $this->targetPlayer;
	}

	public function setTargetPlayer(?Human $player) : void{
		$this->targetPlayer = $player;
	}

	public function entityBaseTick(int $tickDiff = 1): bool{
		// Code below was taken from PMMP's Entity class (grandparent of this class) so that it still ticks normally...
		$this->justCreated = \false;

		$changedProperties = $this->propertyManager->getDirty();
		if(!empty($changedProperties)){
			$this->sendData($this->hasSpawned, $changedProperties);
			$this->propertyManager->clearDirtyProperties();
		}

		$hasUpdate = \false;

		$this->checkBlockCollision();

		if($this->y <= -16 and $this->isAlive()){
			$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_VOID, 10);
			$this->attack($ev);
			$hasUpdate = \true;
		}

		if($this->isOnFire()){
			$hasUpdate = ($hasUpdate || $this->doOnFireTick($tickDiff));
		}

		if($this->noDamageTicks > 0){
			$this->noDamageTicks -= $tickDiff;
			if($this->noDamageTicks < 0){
				$this->noDamageTicks = 0;
			}
		}

		$this->age += $tickDiff;
		$this->ticksLived += $tickDiff;


		/////////////////////////////////////////// real thing /////////////////////////////////////////////////////////
		if($this->isClosed()){
			return true;
		}
		if($this->age > Main::$XPTicksTillDespawn){
			$this->close();
			return true;
		}

		$currentTarget = $this->getTargetPlayer();

		if($this->lookForTargetTime >= 20){
			if($currentTarget === null or $currentTarget->distanceSquared($this) > self::MAX_TARGET_DISTANCE ** 2){
				$this->setTargetPlayer(null);

				$newTarget = $this->level->getNearestEntity($this, self::MAX_TARGET_DISTANCE, Human::class);

				if($newTarget instanceof Human and !($newTarget instanceof Player and $newTarget->isSpectator())){
					$currentTarget = $newTarget;
					$this->setTargetPlayer($currentTarget);
				}
			}

			$this->lookForTargetTime = 0;
		}else{
			$this->lookForTargetTime += $tickDiff;
		}

		if($currentTarget !== null){
			$vector = $currentTarget->subtract($this)->add(0, $currentTarget->getEyeHeight() / 2, 0)->divide(self::MAX_TARGET_DISTANCE);

			$distance = $vector->length();
			$oneMinusDistance = (1 - $distance) ** 2;

			if($oneMinusDistance > 0){
				$this->motionX += $vector->x / $distance * $oneMinusDistance * 0.2;
				$this->motionY += $vector->y / $distance * $oneMinusDistance * 0.2;
				$this->motionZ += $vector->z / $distance * $oneMinusDistance * 0.2;
			}

			$canPickup = true;
			if(Main::$XPPickupDelay && !$currentTarget->canPickupXp()){
				$canPickup = false;
			}

			if($canPickup and $this->boundingBox->intersectsWith($currentTarget->getBoundingBox())){
				$currentTarget->addXp($this->getXpValue());
				if(Main::$XPPickupDelay){
					$currentTarget->resetXpCooldown();
				}
				$this->close();

				//TODO: check Mending enchantment
			}
		}

		return $hasUpdate;
	}
}