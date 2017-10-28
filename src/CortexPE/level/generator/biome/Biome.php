<?php

declare(strict_types = 1);

namespace CortexPE\level\generator\biome;

use CortexPE\level\generator\ender\EnderBiome;
use pocketmine\level\generator\hell\HellBiome;

abstract class Biome extends \pocketmine\level\generator\biome\Biome {

	const END = 9;
	const FROZEN_OCEAN = 10;
	const FROZEN_RIVER = 11;

	const ICE_MOUNTAINS = 13;
	const MUSHROOM_ISLAND = 14;
	const MUSHROOM_ISLAND_SHORE = 15;
	const BEACH = 16;
	const DESERT_HILLS = 17;
	const FOREST_HILLS = 18;
	const TAIGA_HILLS = 19;

	const BIRCH_FOREST_HILLS = 28;
	const ROOFED_FOREST = 29;
	const COLD_TAIGA = 30;
	const COLD_TAIGA_HILLS = 31;
	const MEGA_TAIGA = 32;
	const MEGA_TAIGA_HILLS = 33;
	const EXTREME_HILLS_PLUS = 34;
	const SAVANNA = 35;
	const SAVANNA_PLATEAU = 36;
	const MESA = 37;
	const MESA_PLATEAU_F = 38;
	const MESA_PLATEAU = 39;

	const VOID = 127;

	public static function init(){
		parent::init();

		self::register(self::HELL, new HellBiome());
		self::register(self::END, new EnderBiome());
		// TODO: ADD Other Biomes
	}
}