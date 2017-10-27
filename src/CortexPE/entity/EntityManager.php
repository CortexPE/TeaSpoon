<?php

declare(strict_types=1);

namespace CortexPE\entity;

use CortexPE\entity\projectile\Egg;
use CortexPE\entity\projectile\EnderPearl;
use CortexPE\entity\projectile\Snowball;
use pocketmine\entity\Entity;

class EntityManager extends Entity{
	public static function init() : void{
		self::registerEntity(EnderPearl::class, false, ['EnderPearl', 'minecraft:enderpearl']);
		self::registerEntity(Slime::class, false, ['Slime', 'minecraft:slime']);
		self::registerEntity(Spider::class, false, ['Spider', 'minecraft:spider']);

		// Overwrite
		self::registerEntity(Snowball::class, true, ['Snowball', 'minecraft:snowball']);
		self::registerEntity(Egg::class, true, ['Egg', 'minecraft:egg']);
	}
}