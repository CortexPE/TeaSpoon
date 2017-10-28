<?php

// Andrew Gold - Spooky Scary Skeletons

/**
 * Spooky, scary skeletons
 * Send shivers down your spine
 * Shrieking skulls will shock your soul
 * Seal your doom tonight
 * Spooky, scary skeletons
 * Speak with such a screech
 * You'll shake and shudder in surprise
 * When you hear these zombies shriek
 * We're sorry skeletons, you're so misunderstood
 * You only want to socialize, but I don't think we should
 */

namespace CortexPE\entity;

use pocketmine\entity\Animal;

class SkeletonHorse extends Animal {
	const NETWORK_ID = self::SKELETON_HORSE;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 0;

	public function getName(): string{
		return "Skeleton Horse";
	}

	public function initEntity(){
		$this->setMaxHealth(30);
		parent::initEntity();
	}
}