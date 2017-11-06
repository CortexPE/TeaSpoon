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
 * @link http://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE\item;

use pocketmine\item\{
	Item, ItemFactory
};

class ItemManager {
	public static function init(){
		ItemFactory::registerItem(new Boat(), true);
		ItemFactory::registerItem(new EnchantingBottle());
		ItemFactory::registerItem(new EnderPearl());
		ItemFactory::registerItem(new Potion(), true);
		ItemFactory::registerItem(new LingeringPotion(), true);
		ItemFactory::registerItem(new SplashPotion());
		ItemFactory::registerItem(new FlintSteel(), true);
		ItemFactory::registerItem(new FireCharge());
		ItemFactory::registerItem(new TotemOfUndying());
		ItemFactory::registerItem(new Elytra());
		ItemFactory::registerItem(new FireworkRocket());

		Item::addCreativeItem(Item::get(Item::ENDER_PEARL));
		Item::addCreativeItem(Item::get(Item::ENDER_CHEST));
		Item::addCreativeItem(Item::get(Item::BOTTLE_O_ENCHANTING));
		Item::addCreativeItem(Item::get(Item::FIRE_CHARGE));
		Item::addCreativeItem(Item::get(Item::TOTEM));
		Item::addCreativeItem(Item::get(Item::ELYTRA));
		Item::addCreativeItem(Item::get(Item::FIREWORKS));

		for($i = 0; $i <= 36; $i++){
			Item::addCreativeItem(Item::get(Item::SPLASH_POTION, $i));
		}

		for($i = 0; $i <= 36; $i++){
			Item::addCreativeItem(Item::get(Item::LINGERING_POTION, $i));
		}

		for($i = 0; $i <= 5; $i++){
			Item::addCreativeItem(Item::get(Item::BOAT, $i));
		}
	}
}
