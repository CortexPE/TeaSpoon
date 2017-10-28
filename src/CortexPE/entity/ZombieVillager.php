<?php

namespace CortexPE\entity;

use pocketmine\entity\Zombie;

class ZombieVillager extends Zombie {
	const NETWORK_ID = self::ZOMBIE_VILLAGER;

	public $width = 1.031;
	public $length = 0.891;
	public $height = 2.125;

	public function getName(): string{
		return "Zombie Villager";
	}

	public function initEntity(){
		$this->setMaxHealth(20);
		parent::initEntity();
	}
}