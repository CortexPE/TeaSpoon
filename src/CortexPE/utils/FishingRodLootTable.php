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

use CortexPE\item\Potion;
use CortexPE\Main;
use pocketmine\item\Item;

class FishingRodLootTable {
	// VALUES BASED FROM: https://minecraft.gamepedia.com/Fishing

	/** @var Item[] */
	public static $UN_ENCHANTED_LOOT, $LOS1_ENCHANTED_LOOT, $LOS2_ENCHANTED_LOOT, $LOS3_ENCHANTED_LOOT;

	public static function init(){
		if(Main::$cacheFile->get("date") != strval(date("d-m-y")) || Main::$cacheFile->get("fishingRodLoots", "") == ""){
			// Generate new Arrays
			Main::getInstance()->getLogger()->debug("Generating new FishingLootTable");

			self::initUnenchanted();
			self::initLOS1();
			self::initLOS2();
			self::initLOS3();

			shuffle(self::$UN_ENCHANTED_LOOT);
			shuffle(self::$LOS1_ENCHANTED_LOOT);
			shuffle(self::$LOS2_ENCHANTED_LOOT);
			shuffle(self::$LOS3_ENCHANTED_LOOT);

			$fishingRodLoots = [];
			$fishingRodLoots[] = base64_encode(serialize(self::$UN_ENCHANTED_LOOT));
			$fishingRodLoots[] = base64_encode(serialize(self::$LOS1_ENCHANTED_LOOT));
			$fishingRodLoots[] = base64_encode(serialize(self::$LOS2_ENCHANTED_LOOT));
			$fishingRodLoots[] = base64_encode(serialize(self::$LOS3_ENCHANTED_LOOT));

			Main::$cacheFile->set("fishingRodLoots", $fishingRodLoots);
			Main::$cacheFile->save(true);
		} else {
			// Load Arrays
			Main::getInstance()->getLogger()->debug("Loading existing FishingLootTable from cache...");
			$fishingRodLoots = Main::$cacheFile->get("fishingRodLoots");
			self::$UN_ENCHANTED_LOOT = unserialize(base64_decode($fishingRodLoots[0]));
			self::$LOS1_ENCHANTED_LOOT = unserialize(base64_decode($fishingRodLoots[1]));
			self::$LOS2_ENCHANTED_LOOT = unserialize(base64_decode($fishingRodLoots[2]));
			self::$LOS3_ENCHANTED_LOOT = unserialize(base64_decode($fishingRodLoots[3]));
		}
	}

	private static function initUnenchanted(){
		//count = Percentage x 1000
		$arr = "UN_ENCHANTED_LOOT";
		// Fishes
		self::loopedEntry(self::getItem(Item::RAW_FISH), 510, $arr);
		self::loopedEntry(self::getItem(Item::RAW_SALMON), 212, $arr);
		self::loopedEntry(self::getItem(Item::CLOWNFISH), 17, $arr);
		self::loopedEntry(self::getItem(Item::CLOWNFISH), 111, $arr);

		// Treasure
		self::loopedEntry(self::getItem(Item::BOW), 8, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::ENCHANTED_BOOK), 8, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::FISHING_ROD), 8, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::NAME_TAG), 8, $arr);
		self::loopedEntry(self::getItem(Item::SADDLE), 8, $arr);
		self::loopedEntry(self::getItem(Item::LILY_PAD), 8, $arr);

		// Junk
		self::loopedEntry(self::getItem(Item::BOWL), 12, $arr);
		self::loopedEntry(self::getItem(Item::FISHING_ROD), 2, $arr);
		self::loopedEntry(self::getItem(Item::LEATHER), 12, $arr);
		self::loopedEntry(self::getItem(Item::LEATHER_BOOTS), 12, $arr);
		self::loopedEntry(self::getItem(Item::ROTTEN_FLESH), 12, $arr);
		self::loopedEntry(self::getItem(Item::STICK), 6, $arr);
		self::loopedEntry(self::getItem(Item::STRING), 6, $arr);
		self::loopedEntry(self::getItem(Item::POTION)->setDamage(Potion::WATER_BOTTLE), 12, $arr); // water bottle
		self::loopedEntry(self::getItem(Item::BONE), 12, $arr);
		self::loopedEntry(self::getItem(Item::DYE)->setDamage(0), 1, $arr); // ink sac
		self::loopedEntry(self::getItem(Item::TRIPWIRE_HOOK), 12, $arr);
	}

	private static function initLOS1(){
		//count = Percentage x 1000
		$arr = "LOS1_ENCHANTED_LOOT";
		// Fishes
		self::loopedEntry(self::getItem(Item::RAW_FISH), 509, $arr);
		self::loopedEntry(self::getItem(Item::RAW_SALMON), 212, $arr);
		self::loopedEntry(self::getItem(Item::CLOWNFISH), 17, $arr);
		self::loopedEntry(self::getItem(Item::CLOWNFISH), 110, $arr);

		// Treasure
		self::loopedEntry(self::getItem(Item::BOW), 12, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::ENCHANTED_BOOK), 12, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::FISHING_ROD), 12, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::NAME_TAG), 12, $arr);
		self::loopedEntry(self::getItem(Item::SADDLE), 12, $arr);
		self::loopedEntry(self::getItem(Item::LILY_PAD), 12, $arr);

		// Junk
		self::loopedEntry(self::getItem(Item::BOWL), 10, $arr);
		self::loopedEntry(self::getItem(Item::FISHING_ROD), 2, $arr);
		self::loopedEntry(self::getItem(Item::LEATHER), 10, $arr);
		self::loopedEntry(self::getItem(Item::LEATHER_BOOTS), 10, $arr);
		self::loopedEntry(self::getItem(Item::ROTTEN_FLESH), 10, $arr);
		self::loopedEntry(self::getItem(Item::STICK), 5, $arr);
		self::loopedEntry(self::getItem(Item::STRING), 5, $arr);
		self::loopedEntry(self::getItem(Item::POTION)->setDamage(Potion::WATER_BOTTLE), 10, $arr); // water bottle
		self::loopedEntry(self::getItem(Item::BONE), 10, $arr);
		self::loopedEntry(self::getItem(Item::DYE)->setDamage(0), 1, $arr); // ink sac
		self::loopedEntry(self::getItem(Item::TRIPWIRE_HOOK), 10, $arr);
	}

	private static function initLOS2(){
		//count = Percentage x 1000
		$arr = "LOS2_ENCHANTED_LOOT";
		// Fishes
		self::loopedEntry(self::getItem(Item::RAW_FISH), 508, $arr);
		self::loopedEntry(self::getItem(Item::RAW_SALMON), 212, $arr);
		self::loopedEntry(self::getItem(Item::CLOWNFISH), 17, $arr);
		self::loopedEntry(self::getItem(Item::CLOWNFISH), 110, $arr);

		// Treasure
		self::loopedEntry(self::getItem(Item::BOW), 15, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::ENCHANTED_BOOK), 15, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::FISHING_ROD), 15, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::NAME_TAG), 15, $arr);
		self::loopedEntry(self::getItem(Item::SADDLE), 15, $arr);
		self::loopedEntry(self::getItem(Item::LILY_PAD), 15, $arr);

		// Junk
		self::loopedEntry(self::getItem(Item::BOWL), 7, $arr);
		self::loopedEntry(self::getItem(Item::FISHING_ROD), 1, $arr);
		self::loopedEntry(self::getItem(Item::LEATHER), 7, $arr);
		self::loopedEntry(self::getItem(Item::LEATHER_BOOTS), 7, $arr);
		self::loopedEntry(self::getItem(Item::ROTTEN_FLESH), 7, $arr);
		self::loopedEntry(self::getItem(Item::STICK), 4, $arr);
		self::loopedEntry(self::getItem(Item::STRING), 4, $arr);
		self::loopedEntry(self::getItem(Item::POTION)->setDamage(Potion::WATER_BOTTLE), 7, $arr); // water bottle
		self::loopedEntry(self::getItem(Item::BONE), 7, $arr);
		self::loopedEntry(self::getItem(Item::DYE)->setDamage(0), 1, $arr); // ink sac
		self::loopedEntry(self::getItem(Item::TRIPWIRE_HOOK), 7, $arr);
	}

	private static function initLOS3(){
		//count = Percentage x 1000
		$arr = "LOS3_ENCHANTED_LOOT";
		// Fishes
		self::loopedEntry(self::getItem(Item::RAW_FISH), 507, $arr);
		self::loopedEntry(self::getItem(Item::RAW_SALMON), 211, $arr);
		self::loopedEntry(self::getItem(Item::CLOWNFISH), 17, $arr);
		self::loopedEntry(self::getItem(Item::CLOWNFISH), 110, $arr);

		// Treasure
		self::loopedEntry(self::getItem(Item::BOW), 19, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::ENCHANTED_BOOK), 19, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::FISHING_ROD), 19, $arr); // TODO: ADD ENCHANTS
		self::loopedEntry(self::getItem(Item::NAME_TAG), 19, $arr);
		self::loopedEntry(self::getItem(Item::SADDLE), 19, $arr);
		self::loopedEntry(self::getItem(Item::LILY_PAD), 19, $arr);

		// Junk
		self::loopedEntry(self::getItem(Item::BOWL), 5, $arr);
		self::loopedEntry(self::getItem(Item::FISHING_ROD), 1, $arr);
		self::loopedEntry(self::getItem(Item::LEATHER), 5, $arr);
		self::loopedEntry(self::getItem(Item::LEATHER_BOOTS), 5, $arr);
		self::loopedEntry(self::getItem(Item::ROTTEN_FLESH), 5, $arr);
		self::loopedEntry(self::getItem(Item::STICK), 2, $arr);
		self::loopedEntry(self::getItem(Item::STRING), 2, $arr);
		self::loopedEntry(self::getItem(Item::POTION)->setDamage(Potion::WATER_BOTTLE), 5, $arr); // water bottle
		self::loopedEntry(self::getItem(Item::BONE), 5, $arr);
		self::loopedEntry(self::getItem(Item::DYE)->setDamage(0), 1, $arr); // ink sac
		self::loopedEntry(self::getItem(Item::TRIPWIRE_HOOK), 5, $arr);
	}


	private static function loopedEntry(Item $item, int $count, string $arrayName){
		for($i = 0; $i <= $count; $i++){
			self::${$arrayName}[] = $item;
		}
	}

	private static function getItem(int $id) : Item {
		return Item::get($id, 0, 1);
	}

	public static function getRandom(int $level) : Item {
		switch($level){
			default: // hehe peeps be like getting no-effect Luck Of The Sea enchanted rods xD
			case 0:
				return self::$UN_ENCHANTED_LOOT[array_rand(self::$UN_ENCHANTED_LOOT)];
			case 1:
				return self::$LOS1_ENCHANTED_LOOT[array_rand(self::$LOS1_ENCHANTED_LOOT)];
			case 2:
				return self::$LOS2_ENCHANTED_LOOT[array_rand(self::$LOS2_ENCHANTED_LOOT)];
			case 3:
				return self::$LOS3_ENCHANTED_LOOT[array_rand(self::$LOS3_ENCHANTED_LOOT)];
		}
	}
}