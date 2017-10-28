<?php

// BS (For now) #BlamePMMP // Please add Player->addXp() :/

namespace CortexPE\entity;

use pocketmine\entity\Entity;
use pocketmine\Player;

//use pocketmine\event\player\PlayerPickupExpOrbEvent;
//use pocketmine\level\sound\ExpPickupSound;

class XPOrb extends Entity {
	const NETWORK_ID = self::XP_ORB;

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;

	protected $gravity = 0.04;
	protected $drag = 0;

	protected $experience = 0;

	protected $range = 6;

	public function initEntity(){
		parent::initEntity();
		if(isset($this->namedtag->Experience)){
			$this->experience = $this->namedtag["Experience"];
		}else $this->close();
	}

	public function onUpdate($currentTick): bool{
		if($this->closed){
			return false;
		}

		$tickDiff = $currentTick - $this->lastUpdate;

		$this->lastUpdate = $currentTick;

		$this->timings->startTiming();

		$hasUpdate = $this->entityBaseTick($tickDiff);

		$this->age++;

		if($this->age > 1200){
			$this->kill();
			$this->close();
			$hasUpdate = true;
		}

		$minDistance = PHP_INT_MAX;
		$target = null;
		foreach($this->getViewers() as $p){
			if(!$p->isSpectator() and $p->isAlive()){
				if(($dist = $p->distance($this)) < $minDistance and $dist < $this->range && $target instanceof Player){
					$target = $p;
					$minDistance = $dist;
					if(is_null($this->level)){
						$this->level = $p->getLevel();
					}
				}
			}
		}

		if($target instanceof Player){
			$moveSpeed = 0.7;
			$motX = ($target->getX() - $this->x) / 8;
			$motY = ($target->getY() + $target->getEyeHeight() - $this->y) / 8;
			$motZ = ($target->getZ() - $this->z) / 8;
			$motSqrt = sqrt($motX * $motX + $motY * $motY + $motZ * $motZ);
			$motC = 1 - $motSqrt;

			if($motC > 0){
				$motC *= $motC;
				$this->motionX = $motX / $motSqrt * $motC * $moveSpeed;
				$this->motionY = $motY / $motSqrt * $motC * $moveSpeed;
				$this->motionZ = $motZ / $motSqrt * $motC * $moveSpeed;
			}

			/** @noinspection PhpUndefinedVariableInspection */ // $dist can't be undefined. cuz of line 69.
			if($dist <= 1.3){
				//if($target->canPickupXp()){
				//$target->getServer()->getPluginManager()->callEvent($ev = new PlayerPickupExpOrbEvent($target, $this->getExperience()));
				//if(!$ev->isCancelled()){
				$this->kill();
				$this->close();
				//if($this->getExperience() > 0){
				//	$target->getLevel()->addSound(new ExpPickupSound($target, mt_rand(0, 1000)));
				//	$target->addXp($this->getExperience());
				//	$target->resetXpCooldown();
				//}

				return true;
				//}
				//}
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

	public function getExperience(){
		return $this->experience;
	}

	public function setExperience($exp){
		$this->experience = $exp;
	}
}
