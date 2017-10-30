<?php

namespace CortexPE\entity;

use pocketmine\entity\Entity;

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
		return false; // TODO: Implement this.
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
