<?php

declare(strict_types = 1);

namespace CortexPE\entity\projectile;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Throwable;
use pocketmine\level\particle\DestroyBlockParticle;

class Egg extends Throwable {
	const NETWORK_ID = self::EGG;

	const RAND_POS_X = [-0.5, 0, 0.5];
	const RAND_POS_Y = [0, 1];
	const RAND_POS_Z = [-0.5, 0, 0.5];
	const RAND_SCALE = [0.75, 1];

	public function onUpdate(int $currentTick): bool{
		if($this->isCollided || $this->age > 1200){
			if(mt_rand(1, 8) == 4){
				$nbt = Entity::createBaseNBT($this);
				$chick = Entity::createEntity("Chicken", $this->getLevel(), $nbt);
				$chick->setScale(self::RAND_SCALE[array_rand(self::RAND_SCALE)]);
				$chick->spawnToAll();
			} elseif(mt_rand(1,32) == 16){
				for($c = 1; $c <= 4; $c++){
					$randomX = self::RAND_POS_X[array_rand(self::RAND_POS_X)];
					$randomY = self::RAND_POS_Y[array_rand(self::RAND_POS_Y)];
					$randomZ = self::RAND_POS_Z[array_rand(self::RAND_POS_Z)];

					$nbt = Entity::createBaseNBT($this->add($randomX, $randomY, $randomZ));
					$chick = Entity::createEntity("Chicken", $this->getLevel(), $nbt);
					$chick->setScale(0.75); // A bit hacky. but just wait till PMMP adds proper Mob Aging.
					$chick->spawnToAll();
				}
			}
			$this->getLevel()->addParticle(new DestroyBlockParticle($this, Block::get(Block::BROWN_MUSHROOM_BLOCK)));

			$this->close();
		}

		return parent::onUpdate($currentTick);
	}
}