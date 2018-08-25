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

namespace CortexPE\item\utils;

use pocketmine\item\Item;

class ArmorDurability {
	// Just to make it more organized...
	// VALUES ARE BASED FROM: https://minecraft.gamepedia.com/Helmet, https://minecraft.gamepedia.com/Chestplate, https://minecraft.gamepedia.com/Leggings, https://minecraft.gamepedia.com/Boots
	/** @var int[] */
	public const
		LEATHER_DURABILITY = [
		Item::LEATHER_HELMET     => 56,
		Item::LEATHER_CHESTPLATE => 81,
		Item::LEATHER_LEGGINGS   => 76,
		Item::LEATHER_BOOTS      => 66,
	],
		CHAIN_DURABILITY = [
		Item::CHAIN_HELMET     => 166,
		Item::CHAIN_CHESTPLATE => 241,
		Item::CHAIN_LEGGINGS   => 226,
		Item::CHAIN_BOOTS      => 196,
	],
		IRON_DURABILITY = [
		Item::IRON_HELMET     => 166,
		Item::IRON_CHESTPLATE => 241,
		Item::IRON_LEGGINGS   => 226,
		Item::IRON_BOOTS      => 196,
	],
		GOLD_DURABILITY = [
		Item::GOLD_HELMET     => 78,
		Item::GOLD_CHESTPLATE => 113,
		Item::GOLD_LEGGINGS   => 102,
		Item::GOLD_BOOTS      => 92,
	],
		DIAMOND_DURABILITY = [
		Item::DIAMOND_HELMET     => 364,
		Item::DIAMOND_CHESTPLATE => 529,
		Item::DIAMOND_LEGGINGS   => 496,
		Item::DIAMOND_BOOTS      => 430,
	];

	/** @var int */
	public const DURABILITY = [
		Item::LEATHER_HELMET     => 56,
		Item::LEATHER_CHESTPLATE => 81,
		Item::LEATHER_LEGGINGS   => 76,
		Item::LEATHER_BOOTS      => 66,

		Item::CHAIN_HELMET     => 166,
		Item::CHAIN_CHESTPLATE => 241,
		Item::CHAIN_LEGGINGS   => 226,
		Item::CHAIN_BOOTS      => 196,

		Item::IRON_HELMET     => 166,
		Item::IRON_CHESTPLATE => 241,
		Item::IRON_LEGGINGS   => 226,
		Item::IRON_BOOTS      => 196,

		Item::GOLD_HELMET     => 78,
		Item::GOLD_CHESTPLATE => 113,
		Item::GOLD_LEGGINGS   => 102,
		Item::GOLD_BOOTS      => 92,

		Item::DIAMOND_HELMET     => 364,
		Item::DIAMOND_CHESTPLATE => 529,
		Item::DIAMOND_LEGGINGS   => 496,
		Item::DIAMOND_BOOTS      => 430,

		Item::ELYTRA => 431,
	];

	/** @var int */
	public const OTHERS = [
		Item::ELYTRA => 431,
	];

	/** @var int[] */
	public const NON_ARMOR_WEARABLES = [
		Item::MOB_HEAD,
		Item::PUMPKIN,
		Item::AIR, // whenever the player isn't wearing something for that inventory slot...
	];

	public static function getDurability(int $id): int{
		if(in_array($id, self::NON_ARMOR_WEARABLES)){
			return -1;
		}
		if(isset(self::DURABILITY[$id])){
			return self::DURABILITY[$id];
		}

		return -1;
	}
}