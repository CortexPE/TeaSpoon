<?php

declare(strict_types=1);

namespace CortexPE\entity;

use pocketmine\entity\Animal;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;

class Bat extends Animal {
	const NETWORK_ID = self::BAT;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 0.6;

	public function getName() : string{
		return "Bat";
	}

	public function initEntity(){
		$this->setMaxHealth(6);
		parent::initEntity();
	}

	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->isResting)){
			$nbt->isResting = new ByteTag("isResting", 0);
		}
		parent::__construct($level, $nbt);

		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_RESTING, $this->isResting());
	}

	public function isResting(): bool{
		return boolval($this->namedtag["isResting"]);
	}

	public function setResting(bool $resting){
		$this->namedtag->isResting = new ByteTag("isResting", $resting ? 1 : 0);
	}

	public function onUpdate(int $currentTick): bool{
		if($this->age > 1200){
			// I have reached the lifespan of my species...
			// Existance is pain.
			// I JUST WANT TO DIE
			// ~ Mr. Meeseeks
			$this->kill();
		}
		return parent::onUpdate($currentTick);
	}
}
