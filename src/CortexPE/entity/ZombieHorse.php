<?php

namespace CortexPE\entity;

use pocketmine\entity\Animal;

class ZombieHorse extends Animal {
    const NETWORK_ID = self::ZOMBIE_HORSE;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 0;

    public function getName(): string {
        return "Zombie Horse";
    }

	public function initEntity(){
		$this->setMaxHealth(20);
		parent::initEntity();
	}
}