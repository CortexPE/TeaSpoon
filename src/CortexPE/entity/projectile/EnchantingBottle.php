<?php

declare(strict_types = 1);

namespace CortexPE\entity\projectile;

use CortexPE\level\particle\SpellParticle;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Throwable;

class EnchantingBottle extends Throwable {
	const NETWORK_ID = self::XP_BOTTLE;

	public $spawnedOrbs = false;

	const RAND_POS_X = [0, -0.1];
	const RAND_POS_Y = [-0.2];
	const RAND_POS_Z = [0, -0.1];

	public function onUpdate(int $currentTick): bool{
		if($this->isCollided || $this->age > 1200 && !$this->spawnedOrbs){
			$rand = mt_rand(1,3);
			$this->getLevel()->addParticle(new SpellParticle($this, 46, 82, 153));
			for($c = 0; $c <= $rand; $c++){
				$randomX = self::RAND_POS_X[array_rand(self::RAND_POS_X)];
				$randomY = self::RAND_POS_Y[array_rand(self::RAND_POS_Y)];
				$randomZ = self::RAND_POS_Z[array_rand(self::RAND_POS_Z)];

				$nbt = Entity::createBaseNBT($this->add($randomX, $randomY, $randomZ));
				$nbt->setLong("Experience", mt_rand(1, 4));
				$orb = Entity::createEntity("XPOrb", $this->getLevel(), $nbt);
				$orb->spawnToAll();
			}
			$this->kill();
		}

		return parent::onUpdate($currentTick);
	}
}