<?php

/**
 *
 * MMP""MM""YMM               .M"""bgd
 * P'   MM   `7              ,MI    "Y
 *      MM  .gP"Ya   ,6"Yb.  `MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.
 *      MM ,M'   Yb 8)   MM    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM
 *      MM 8M""""""  ,pm9MM  .     `MM MM    M8 8M     M8 8M     M8 MM    MM
 *      MM YM.    , 8M   MM  Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM
 *    .JMML.`Mbmmd' `Moo9^Yo.P"Ybmmd"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML.
 *                                     MM
 *                                   .JMML.
 * This file is part of TeaSpoon.
 *
 * TeaSpoon is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeaSpoon is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TeaSpoon.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author CortexPE
 * @link https://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE\level\generator\biome;

use CortexPE\level\generator\ender\biome\EnderBiome;
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