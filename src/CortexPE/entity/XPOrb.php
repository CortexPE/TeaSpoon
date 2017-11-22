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

// Modded to work with PMMP

declare(strict_types = 1);

namespace CortexPE\entity;

use CortexPE\utils\Xp;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\IntTag;

class XPOrb extends Entity {
	const NETWORK_ID = self::XP_ORB;

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;

	protected $gravity = 0.04;
	protected $drag = 0;

	protected $experience = 0;

	protected $pickuprange = 1.2;
	protected $followrange = 6;

	public function initEntity(){
		parent::initEntity();
		if(isset($this->namedtag->Experience)){
			$this->experience = $this->namedtag["Experience"];
		}else $this->close();
	}

	public function entityBaseTick(int $tickDiff = 1): bool{
		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->age > 7000){
			$this->timings->stopTiming();
			$this->close();

			return true;
		}

		if(!$this->onGround){
			$this->motionY -= $this->gravity;
		}

		$Target = $this->FetchNearbyPlayer($this->followrange);
		if($Target instanceof \pocketmine\entity\Human){
			$moveSpeed = 0.5;
			$motX = ($Target->getX() - $this->x) / 8;
			$motY = ($Target->getY()/* + $Target->getEyeHeight() */ - $this->y) / 8;
			$motZ = ($Target->getZ() - $this->z) / 8 /* * (1 / $Target->getZ())*/;
			$motSqrt = sqrt($motX * $motX + $motY * $motY + $motZ * $motZ);
			$motC = 1 - $motSqrt;

			if($motC > 0){
				$motC *= $motC;
				$d = $motSqrt * $motC * $moveSpeed;
				$d = ($d == 0 ? NULL : $d);
				if($d !== NULL){
					$this->motionX = $motX / $d;
					$this->motionY = $motY / $d;
					$this->motionZ = $motZ / $d;
				}
			}

			if($Target->distance($this) <= $this->pickuprange){
				$this->timings->stopTiming();
				$this->close();
				if($this->getExperience() > 0){
					Xp::addXp($Target, $this->getExperience());
				}

				return true;
			}
		}

		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$this->updateMovement();

		$this->timings->stopTiming();

		return $hasUpdate or !$this->onGround or abs($this->motionX) > 0.00001 or abs($this->motionY) > 0.00001 or abs($this->motionZ) > 0.00001;
	}

	public function FetchNearbyPlayer($DistanceRange){
		$MinDistance = $DistanceRange;
		$Target = null;
		foreach($this->getLevel()->getPlayers() as $player){
			if($player->isAlive() and $MinDistance >= $Distance = $player->distance($this)){
				$Target = $player;
				$MinDistance = $Distance;
			}
		}

		return $Target;
	}

	public function getExperience(){
		return $this->experience;
	}

	public function setExperience($exp){
		$this->experience = $exp;
	}

	public function canCollideWith(Entity $entity): bool{
		return false;
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->Experience = new IntTag("Experience", $this->experience);
	}
}
