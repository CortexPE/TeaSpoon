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
use pocketmine\level\biome\HellBiome;

abstract class Biome extends \pocketmine\level\biome\Biome {

    /** @var int */
	public const
        END = 9,
        FROZEN_OCEAN = 10,
        FROZEN_RIVER = 11,

	    ICE_MOUNTAINS = 13,
        MUSHROOM_ISLAND = 14,
        MUSHROOM_ISLAND_SHORE = 15,
        BEACH = 16,
        DESERT_HILLS = 17,
        FOREST_HILLS = 18,
        TAIGA_HILLS = 19,

        BIRCH_FOREST_HILLS = 28,
        ROOFED_FOREST = 29,
        COLD_TAIGA = 30,
        COLD_TAIGA_HILLS = 31,
        MEGA_TAIGA = 32,
        MEGA_TAIGA_HILLS = 33,
        EXTREME_HILLS_PLUS = 34,
        SAVANNA = 35,
        SAVANNA_PLATEAU = 36,
        MESA = 37,
        MESA_PLATEAU_F = 38,
        MESA_PLATEAU = 39,

        VOID = 127;

	public static function init(){
		parent::init();

		self::register(self::HELL, new HellBiome());
		self::register(self::END, new EnderBiome());
		// TODO: ADD Other Biomes
	}
}
