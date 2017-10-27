<?php

declare(strict_types=1);

namespace CortexPE\entity;

use CortexPE\entity\projectile\Egg;
use CortexPE\entity\projectile\EnderPearl;
use CortexPE\entity\projectile\Snowball;
use pocketmine\entity\Entity;

class EntityManager extends Entity{
	public static function init() : void{
		self::registerEntity(Bat::class, false, ['Bat', 'minecraft:bat']);
		self::registerEntity(Blaze::class, false, ['Blaze', 'minecraft:blaze']);
		self::registerEntity(CaveSpider::class, false, ['CaveSpider', 'minecraft:cavespider']);
		self::registerEntity(Chicken::class, false, ['Chicken', 'minecraft:chicken']);
		self::registerEntity(Cow::class, false, ['Cow', 'minecraft:cow']);
		self::registerEntity(Creeper::class, false, ['Creeper', 'minecraft:creeper']);
		self::registerEntity(Donkey::class, false, ['Donkey', 'minecraft:donkey']);
		self::registerEntity(ElderGuardian::class, false, ['ElderGuardian', 'minecraft:elderguardian']);
		self::registerEntity(EnderDragon::class, false, ['EnderDragon', 'minecraft:enderdragon']);
		self::registerEntity(Enderman::class, false, ['enderman', 'minecraft:enderman']);
		self::registerEntity(EnderPearl::class, false, ['EnderPearl', 'minecraft:enderpearl']);
		self::registerEntity(Evoker::class, false, ['Evoker', 'minecraft:evoker']);
		self::registerEntity(Ghast::class, false, ['Ghast', 'minecraft:ghast']);
		self::registerEntity(Guardian::class, false, ['Guardian', 'minecraft:guardian']);
		self::registerEntity(Guardian::class, false, ['Guardian', 'minecraft:guardian']);
		self::registerEntity(Horse::class, false, ['Horse', 'minecraft:horse']);
		self::registerEntity(Husk::class, false, ['Husk', 'minecraft:husk']);
		self::registerEntity(IronGolem::class, false, ['IronGolem', 'minecraft:irongolem']);
		self::registerEntity(Lightning::class, false, ['Lightning', 'minecraft:lightning']);
		self::registerEntity(Llama::class, false, ['Llama', 'minecraft:llama']);
		self::registerEntity(MagmaCube::class, false, ['MagmaCube', 'minecraft:magmacube']);
		self::registerEntity(Mooshroom::class, false, ['Mooshroom', 'minecraft:mooshroom']);
		self::registerEntity(Mule::class, false, ['Mule', 'minecraft:mule']);
		self::registerEntity(Ocelot::class, false, ['Ocelot', 'minecraft:ocelot']);
		// TODO: OTHER MOBS & Entities
		self::registerEntity(Slime::class, false, ['Slime', 'minecraft:slime']);
		self::registerEntity(Spider::class, false, ['Spider', 'minecraft:spider']);

		// Overwrite
		self::registerEntity(Snowball::class, true, ['Snowball', 'minecraft:snowball']);
		self::registerEntity(Egg::class, true, ['Egg', 'minecraft:egg']);
	}
}