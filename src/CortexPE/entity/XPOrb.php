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

use CortexPE\Utils;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\FloatTag;
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

	public function FetchNearbyPlayer($DistanceRange) {
		$MinDistance = $DistanceRange;
		$Target = null;
		foreach ($this->getLevel()->getPlayers() as $player){
			if ($player->isAlive() and $MinDistance >= $Distance = $player->distance($this)){
				$Target = $player;
				$MinDistance = $Distance;
			}
		}
		return $Target;
	}

	public function entityBaseTick(int $tickDiff = 1): bool{
		if ($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if ($this->age > 7000){
			$this->timings->stopTiming();
			$this->close();
			return true;
		}

		if (!$this->onGround){
			$this->motionY -= $this->gravity;
		}

		$Target = $this->FetchNearbyPlayer($this->followrange);
		if ($Target instanceof \pocketmine\entity\Human){
			$moveSpeed = 0.5;
			$motX = ($Target->getX() - $this->x) / 8;
			$motY = ($Target->getY()/* + $Target->getEyeHeight() */ - $this->y) / 8;
			$motZ = ($Target->getZ() - $this->z) / 8 /* * (1 / $Target->getZ())*/
			;
			$motSqrt = sqrt($motX * $motX + $motY * $motY + $motZ * $motZ);
			$motC = 1 - $motSqrt;

			if ($motC > 0){
				$motC *= $motC;
				$this->motionX = $motX / $motSqrt * $motC * $moveSpeed;
				$this->motionY = $motY / $motSqrt * $motC * $moveSpeed;
				$this->motionZ = $motZ / $motSqrt * $motC * $moveSpeed;
			}

			if ($Target->distance($this) <= $this->pickuprange){
				$this->timings->stopTiming();
				$this->close();
				if ($this->getExperience() > 0){
					// [ROT13 Encoded and is pretty Explicit] Jul gur shpx unfa'g CZZC Vzcyrzragrq n CEBCRE KC Flfgrz Lrg? Guvf vf Shpxvat fghcvq naq vf bar bs gur znal ernfbaf jul Crbcyr ybir fcbbaf -_-
					$add = Utils::getLevelFromXp($Target->getTotalXp() + $this->getExperience());
					$Target->setXpProgress($add[1]);
					$Target->setXpLevel(intval($Target->getXpLevel() + round($Target->getXpProgress()))); // hacky code to *SOMEHOW* get it working...

					if(!isset($Target->namedtag->XpLevel) or !($Target->namedtag->XpLevel instanceof IntTag)){
						$Target->namedtag->XpLevel = new IntTag("XpLevel", $Target->getXpLevel());
					}else{
						$Target->namedtag["XpLevel"] = $Target->getXpLevel();
					}

					if(!isset($Target->namedtag->XpP) or !($Target->namedtag->XpP instanceof FloatTag)){
						$Target->namedtag->XpP = new FloatTag("XpP", $Target->getXpProgress());
					} else {
						$Target->namedtag["XpP"] = $Target->getXpProgress();
					}

					if(!isset($Target->namedtag->XpTotal) or !($Target->namedtag->XpTotal instanceof IntTag)){
						$Target->namedtag->XpTotal = new IntTag("XpTotal", $Target->getTotalXp());
					}else{
						$Target->namedtag["XpTotal"] = $Target->getTotalXp();
					}
					$Target->getServer()->saveOfflinePlayerData($Target->getName(),$Target->namedtag, true);
				}
				return true;
			}
		}

		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$this->updateMovement();

		$this->timings->stopTiming();

		return $hasUpdate or !$this->onGround or abs($this->motionX) > 0.00001 or abs($this->motionY) > 0.00001 or abs($this->motionZ) > 0.00001;
	}

	public function canCollideWith(Entity $entity): bool{
		return false;
	}

	public function setExperience($exp){
		$this->experience = $exp;
	}

	public function getExperience(){
		return $this->experience;
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->Experience = new IntTag("Experience", $this->experience);
	}
}
