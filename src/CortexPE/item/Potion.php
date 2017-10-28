<?php

/*
 *     __						    _
 *    / /  _____   _____ _ __ _   _| |
 *   / /  / _ \ \ / / _ \ '__| | | | |
 *  / /__|  __/\ V /  __/ |  | |_| | |
 *  \____/\___| \_/ \___|_|   \__, |_|
 *						      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author LeverylTeam
 * @link https://github.com/LeverylTeam
 *
 * Originally Made by @iTXTech for Genisys
 * Modified and Re-Written for Leveryl by @CortexPE
*/

declare(strict_types = 1);

namespace CortexPE\item;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\Player;
use pocketmine\Server;

class Potion extends Item {

	//No effects
	const WATER_BOTTLE = 0;
	const MUNDANE = 1;
	const MUNDANE_EXTENDED = 2;
	const THICK = 3;
	const AWKWARD = 4;

	//Actual potions
	const NIGHT_VISION = 5;
	const NIGHT_VISION_T = 6;
	const INVISIBILITY = 7;
	const INVISIBILITY_T = 8;
	const LEAPING = 9;
	const LEAPING_T = 10;
	const LEAPING_TWO = 11;
	const FIRE_RESISTANCE = 12;
	const FIRE_RESISTANCE_T = 13;
	const SWIFTNESS = 14;
	const SWIFTNESS_T = 15;
	const SWIFTNESS_TWO = 16;
	const SLOWNESS = 17;
	const SLOWNESS_T = 18;
	const WATER_BREATHING = 19;
	const WATER_BREATHING_T = 20;
	const HEALING = 21;
	const HEALING_TWO = 22;
	const HARMING = 23;
	const HARMING_TWO = 24;
	const POISON = 25;
	const POISON_T = 26;
	const POISON_TWO = 27;
	const REGENERATION = 28;
	const REGENERATION_T = 29;
	const REGENERATION_TWO = 30;
	const STRENGTH = 31;
	const STRENGTH_T = 32;
	const STRENGTH_TWO = 33;
	const WEAKNESS = 34;
	const WEAKNESS_T = 35;
	const DECAY = 36;

	const POTIONS = [
		self::WATER_BOTTLE      => false,
		self::MUNDANE           => false,
		self::MUNDANE_EXTENDED  => false,
		self::THICK             => false,
		self::AWKWARD           => false,
		self::NIGHT_VISION      => [Effect::NIGHT_VISION, (180 * 20), 0],
		self::NIGHT_VISION_T    => [Effect::NIGHT_VISION, (480 * 20), 0],
		self::INVISIBILITY      => [Effect::INVISIBILITY, (180 * 20), 0],
		self::INVISIBILITY_T    => [Effect::INVISIBILITY, (480 * 20), 0],
		self::LEAPING           => [Effect::JUMP, (180 * 20), 0],
		self::LEAPING_T         => [Effect::JUMP, (480 * 20), 0],
		self::LEAPING_TWO       => [Effect::JUMP, (90 * 20), 1],
		self::FIRE_RESISTANCE   => [Effect::FIRE_RESISTANCE, (180 * 20), 0],
		self::FIRE_RESISTANCE_T => [Effect::FIRE_RESISTANCE, (480 * 20), 0],
		self::SWIFTNESS         => [Effect::SPEED, (180 * 20), 0],
		self::SWIFTNESS_T       => [Effect::SPEED, (480 * 20), 0],
		self::SWIFTNESS_TWO     => [Effect::SPEED, (90 * 20), 1],
		self::SLOWNESS          => [Effect::SLOWNESS, (90 * 20), 0],
		self::SLOWNESS_T        => [Effect::SLOWNESS, (240 * 20), 0],
		self::WATER_BREATHING   => [Effect::WATER_BREATHING, (180 * 20), 0],
		self::WATER_BREATHING_T => [Effect::WATER_BREATHING, (480 * 20), 0],
		self::HEALING           => [Effect::HEALING, (1), 0],
		self::HEALING_TWO       => [Effect::HEALING, (1), 1],
		self::HARMING           => [Effect::HARMING, (1), 0],
		self::HARMING_TWO       => [Effect::HARMING, (1), 1],
		self::POISON            => [Effect::POISON, (45 * 20), 0],
		self::POISON_T          => [Effect::POISON, (120 * 20), 0],
		self::POISON_TWO        => [Effect::POISON, (22 * 20), 1],
		self::REGENERATION      => [Effect::REGENERATION, (45 * 20), 0],
		self::REGENERATION_T    => [Effect::REGENERATION, (120 * 20), 0],
		self::REGENERATION_TWO  => [Effect::REGENERATION, (22 * 20), 1],
		self::STRENGTH          => [Effect::STRENGTH, (180 * 20), 0],
		self::STRENGTH_T        => [Effect::STRENGTH, (480 * 20), 0],
		self::STRENGTH_TWO      => [Effect::STRENGTH, (90 * 20), 1],
		self::WEAKNESS          => [Effect::WEAKNESS, (90 * 20), 0],
		self::WEAKNESS_T        => [Effect::WEAKNESS, (240 * 20), 0],
		self::DECAY             => [Effect::WITHER, (40 * 20), 0],
	];

	protected static $POTION_NAMES = [
		self::WATER_BOTTLE      => "Water Bottle",
		self::MUNDANE           => "Mundane Potion",
		self::MUNDANE_EXTENDED  => "Mundane Potion",
		self::THICK             => "Thick Potion",
		self::AWKWARD           => "Awkward Potion",
		self::INVISIBILITY      => "Potion of Invisibility",
		self::INVISIBILITY_T    => "Potion of Invisibility",
		self::LEAPING           => "Potion of Leaping",
		self::LEAPING_T         => "Potion of Leaping",
		self::LEAPING_TWO       => "Potion of Leaping II",
		self::FIRE_RESISTANCE   => "Potion of Fire Resistance",
		self::FIRE_RESISTANCE_T => "Potion of Fire Resistance",
		self::SWIFTNESS         => "Potion of Swiftness",
		self::SWIFTNESS_T       => "Potion of Swiftness",
		self::SWIFTNESS_TWO     => "Potion of Swiftness II",
		self::SLOWNESS          => "Potion of Slowness",
		self::SLOWNESS_T        => "Potion of Slowness II",
		self::WATER_BREATHING   => "Potion of Water Breathing",
		self::WATER_BREATHING_T => "Potion of Water Breathing",
		self::HARMING           => "Potion of Harming",
		self::HARMING_TWO       => "Potion of Harming II",
		self::POISON            => "Potion of Poison",
		self::POISON_T          => "Potion of Poison",
		self::POISON_TWO        => "Potion of Poison II",
		self::HEALING           => "Potion of Healing",
		self::HEALING_TWO       => "Potion of Healing II",
		self::NIGHT_VISION      => "Potion of Night Vision",
		self::NIGHT_VISION_T    => "Potion of Night Vision",
		self::STRENGTH          => "Potion of Strength",
		self::STRENGTH_T        => "Potion of Strength",
		self::STRENGTH_TWO      => "Potion of Strength II",
		self::REGENERATION      => "Potion of Regeneration",
		self::REGENERATION_T    => "Potion of Regeneration",
		self::REGENERATION_TWO  => "Potion of Regeneration II",
		self::WEAKNESS          => "Potion of Weakness",
		self::WEAKNESS_T        => "Potion of Weakness",
		self::DECAY             => "Potion of Wither II",
	];

	//Structure: Potion ID => [matching effect, duration in ticks, amplifier]
	//Use false if no effects.
	protected static $POTION_EFFECTS = [
		self::WATER_BOTTLE      => false,
		self::MUNDANE           => false,
		self::MUNDANE_EXTENDED  => false,
		self::THICK             => false,
		self::AWKWARD           => false,
		self::NIGHT_VISION      => [
			[Effect::NIGHT_VISION, (180 * 20), 0],
		],
		self::NIGHT_VISION_T    => [
			[Effect::NIGHT_VISION, (480 * 20), 0],
		],
		self::INVISIBILITY      => [
			[Effect::INVISIBILITY, (180 * 20), 0],
		],
		self::INVISIBILITY_T    => [
			[Effect::INVISIBILITY, (480 * 20), 0],
		],
		self::LEAPING           => [
			[Effect::JUMP, (180 * 20), 0],
		],
		self::LEAPING_T         => [
			[Effect::JUMP, (480 * 20), 0],
		],
		self::LEAPING_TWO       => [
			[Effect::JUMP, (90 * 20), 1],
		],
		self::FIRE_RESISTANCE   => [
			[Effect::FIRE_RESISTANCE, (180 * 20), 0],
		],
		self::FIRE_RESISTANCE_T => [
			[Effect::FIRE_RESISTANCE, (480 * 20), 0],
		],
		self::SWIFTNESS         => [
			[Effect::SPEED, (180 * 20), 0],
		],
		self::SWIFTNESS_T       => [
			[Effect::SPEED, (480 * 20), 0],
		],
		self::SWIFTNESS_TWO     => [
			[Effect::SPEED, (90 * 20), 1],
		],
		self::SLOWNESS          => [
			[Effect::SLOWNESS, (90 * 20), 0],
		],
		self::SLOWNESS_T        => [
			[Effect::SLOWNESS, (240 * 20), 0],
		],
		self::WATER_BREATHING   => [
			[Effect::WATER_BREATHING, (180 * 20), 0],
		],
		self::WATER_BREATHING_T => [
			[Effect::WATER_BREATHING, (480 * 20), 0],
		],
		self::HEALING           => [
			[Effect::HEALING, (1), 0],
		],
		self::HEALING_TWO       => [
			[Effect::HEALING, (1), 1],
		],
		self::HARMING           => [
			[Effect::HARMING, (1), 0],
		],
		self::HARMING_TWO       => [
			[Effect::HARMING, (1), 1],
		],
		self::POISON            => [
			[Effect::POISON, (45 * 20), 0],
		],
		self::POISON_T          => [
			[Effect::POISON, (120 * 20), 0],
		],
		self::POISON_TWO        => [
			[Effect::POISON, (22 * 20), 1],
		],
		self::REGENERATION      => [
			[Effect::REGENERATION, (45 * 20), 0],
		],
		self::REGENERATION_T    => [
			[Effect::REGENERATION, (120 * 20), 0],
		],
		self::REGENERATION_TWO  => [
			[Effect::REGENERATION, (22 * 20), 1],
		],
		self::STRENGTH          => [
			[Effect::STRENGTH, (180 * 20), 0],
		],
		self::STRENGTH_T        => [
			[Effect::STRENGTH, (480 * 20), 0],
		],
		self::STRENGTH_TWO      => [
			[Effect::STRENGTH, (90 * 20), 1],
		],
		self::WEAKNESS          => [
			[Effect::WEAKNESS, (90 * 20), 0],
		],
		self::WEAKNESS_T        => [
			[Effect::WEAKNESS, (240 * 20), 0],
		],
		self::DECAY             => [
			[Effect::WITHER, (40 * 20), 0],
		],
	];

	protected static $POTION_EFFECT_ID = [
		self::INVISIBILITY      => Effect::INVISIBILITY,
		self::INVISIBILITY_T    => Effect::INVISIBILITY,
		self::LEAPING           => Effect::JUMP,
		self::LEAPING_T         => Effect::JUMP,
		self::LEAPING_TWO       => Effect::JUMP,
		self::FIRE_RESISTANCE   => Effect::FIRE_RESISTANCE,
		self::FIRE_RESISTANCE_T => Effect::FIRE_RESISTANCE,
		self::SWIFTNESS         => Effect::SPEED,
		self::SWIFTNESS_T       => Effect::SPEED,
		self::SWIFTNESS_TWO     => Effect::SPEED,
		self::SLOWNESS          => Effect::SLOWNESS,
		self::SLOWNESS_T        => Effect::SLOWNESS,
		self::WATER_BREATHING   => Effect::WATER_BREATHING,
		self::WATER_BREATHING_T => Effect::WATER_BREATHING,
		self::HARMING           => Effect::HARMING,
		self::HARMING_TWO       => Effect::HARMING,
		self::POISON            => Effect::POISON,
		self::POISON_T          => Effect::POISON,
		self::POISON_TWO        => Effect::POISON,
		self::HEALING           => Effect::HEALING,
		self::HEALING_TWO       => Effect::HEALING,
		self::NIGHT_VISION      => Effect::NIGHT_VISION,
		self::NIGHT_VISION_T    => Effect::NIGHT_VISION,
		self::REGENERATION      => Effect::REGENERATION,
		self::REGENERATION_T    => Effect::REGENERATION,
		self::REGENERATION_TWO  => Effect::REGENERATION,
	];

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::POTION, $meta, self::getNameByMeta($meta));
	}

	public static function getNameByMeta(int $meta): string{
		return self::$POTION_NAMES[$meta] ?? "Potion";
	}

	public static function getColor(int $meta){
		$effect = Effect::getEffect(self::getEffectId($meta));

		if($effect === null){
			return [46, 82, 153]; // Default to Blue
		}

		return $effect->getColor() ?? [0, 0, 0];
	}

	public static function getEffectId(int $meta): int{
		return self::$POTION_EFFECT_ID[$meta] ?? 0;
	}

	/**
	 * Registers a Custom Potion
	 *
	 * @param int $id
	 * @param string $name
	 * @param array $effects
	 * @param Int $color
	 * @param Server $server
	 *
	 * @return bool
	 */
	public static function registerPotion(int $id, string $name, array $effects, Int $color, Server $server){
		if(isset(self::$POTION_NAMES[$id])){ // So it wouldn't mess with other potions
			$server->getLogger()->warning("Unable to register Potion ID: " . $id);

			return false;
		}else{
			self::$POTION_NAMES[$id] = $name;
			self::$POTION_EFFECTS[$id] = $effects;
			self::$POTION_EFFECT_ID[$id] = $color;

			$server->getLogger()->info("Successfully Registered Potion ID: " . $id);

			return true;
		}
	}

	/**
	 * @param int $id
	 * @return Effect[]
	 */
	public static function getEffectsById(int $id): array{
		if(self::$POTION_EFFECTS[$id] === false){
			return [];
		}
		foreach(self::$POTION_EFFECTS[$id] as $effs){ // $effs is an array.
			if(count($effs ?? []) === 3){ // Count
				if($effs[2] < 2147483646){ // So they can't make potions higher than the limit
					$effects[] = Effect::getEffect($effs[0])->setDuration($effs[1])->setAmplifier($effs[2]);
				}
			}
		}

		return $effects ?? [];
	}

	public function getMaxStackSize(): int{
		return 1;
	}

	public function canBeConsumed(): bool{
		return $this->meta > 0;
	}

	public function canBeConsumedBy(Entity $entity): bool{
		return $entity instanceof Human;
	}

	public function onConsume(Entity $human){
		$pk = new EntityEventPacket();
		$pk->entityRuntimeId = $human->getId();
		$pk->event = EntityEventPacket::USE_ITEM;
		if($human instanceof Player){
			$human->dataPacket($pk);
		}
		$server = $human->getLevel()->getServer();

		$server->broadcastPacket($human->getViewers(), $pk);

		//($ev = new EntityDrinkPotionEvent($human, $this))->call();

		//if(!$ev->isCancelled()){
		foreach($this->getEffects() as $effect){
			$human->addEffect($effect);
		}
		//Don't set the held item to glass bottle if we're in creative
		if($human instanceof Player){
			if($human->getGamemode() === 1){
				return;
			}
		}
		$human->getInventory()->setItemInHand(Item::get(self::GLASS_BOTTLE));
		//}
	}

	public function getEffects(): array{
		/*
		 * Structure:
		 * POTION_EFFECTS: - $POTION_EFFECTS array
		 *      Meta: - $this->meta
		 *          Effects: $effs
		 *              - Effect ID, Effect Duration, Effect Amplifier - COUNT
		 *
		 * THIS IS A FREAKIN ARRAY ON AN ARRAY ON AN ARRAY...
		 *
		 * Sample:
		 * protected static $POTION_EFFECTS = [
		 *      self::META => [
		 *           [Effect::EFFECT_NAME, Duration_Ticks, Amplifier]
		 *      ]
		 * ]
		 */
		foreach(self::$POTION_EFFECTS[$this->meta] as $effs){ // $effs is an array.
			if(count($effs ?? []) === 3){ // Count
				if($effs[2] < 2147483646){ // So they can't make potions higher than the limit
					$effects[] = Effect::getEffect($effs[0])->setDuration($effs[1])->setAmplifier($effs[2]);
				}
			}
		}

		return $effects ?? [];
	}
}
