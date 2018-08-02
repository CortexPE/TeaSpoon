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

namespace CortexPE\utils;

use CortexPE\Utils;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;

class Xp extends Utils {
	public static function getXpDropsForEntity(Entity $e): int{
		switch($e::NETWORK_ID){
			// animals //
			case Entity::CHICKEN:
			case Entity::COW:
			case Entity::HORSE:
			case Entity::DONKEY:
			case Entity::MULE:
			case Entity::SKELETON_HORSE:
			case Entity::ZOMBIE_HORSE:
			case Entity::MOOSHROOM:
			case Entity::LLAMA:
			case Entity::OCELOT:
			case Entity::PARROT:
			case Entity::PIG:
			case Entity::POLAR_BEAR:
			case Entity::SHEEP:
			case Entity::SQUID:
			case Entity::RABBIT:
			case Entity::WOLF:
				return mt_rand(1, 3);
			case Entity::BAT:
				return 0;
			// golems //
			case Entity::IRON_GOLEM:
			case Entity::SNOW_GOLEM:
				return 0;
			// monsters //
			case Entity::CAVE_SPIDER:
			case Entity::CREEPER:
			case Entity::ENDERMAN:
			case Entity::GHAST:
			case Entity::HUSK:
			case Entity::SHULKER:
			case Entity::SILVERFISH:
			case Entity::SKELETON:
			case Entity::SPIDER:
			case Entity::STRAY:
			case Entity::VINDICATOR:
			case Entity::WITCH:
			case Entity::WITHER_SKELETON:
			case Entity::ZOMBIE:
			case Entity::ZOMBIE_PIGMAN:
				return 5;
			case Entity::ENDERMITE:
			case Entity::VEX:
				return 3;
			case Entity::SLIME:
			case Entity::MAGMA_CUBE:
				return mt_rand(1, 4);
			case Entity::BLAZE:
			case Entity::GUARDIAN:
			case Entity::ELDER_GUARDIAN:
			case Entity::EVOCATION_ILLAGER:
				return 10;
			case Human::NETWORK_ID: // Handled by PMMP ;)
			case Entity::VILLAGER:
				return 0;
			case Entity::ENDER_DRAGON:
				return (boolval(rand(0, 1)) ? 12000 : 500);
			case Entity::WITHER:
				return 50;
			case Entity::LIGHTNING_BOLT:
				return 0;
		}

		return 0;
	}
}