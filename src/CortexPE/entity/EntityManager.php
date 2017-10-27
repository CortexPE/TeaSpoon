<?php

declare(strict_types=1);

namespace CortexPE\entity;

use CortexPE\entity\projectile\Egg;
use CortexPE\entity\projectile\EnderPearl;
use CortexPE\entity\projectile\Snowball;
use pocketmine\entity\Entity;

class EntityManager {
	public static function init(){
		Entity::registerEntity(EnderPearl::class, false, ['EnderPearl', 'minecraft:enderpearl']);

		// Overwrite
		Entity::registerEntity(Snowball::class, true, ['Snowball', 'minecraft:snowball']);
		Entity::registerEntity(Egg::class, true, ['Egg', 'minecraft:egg']);
	}
}