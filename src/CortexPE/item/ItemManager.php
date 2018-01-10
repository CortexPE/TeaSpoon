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

namespace CortexPE\item;

use pocketmine\item\{
	Item, ItemFactory
};

class ItemManager {
	public static function init(){
		ItemFactory::registerItem(new Boat(), true);
		ItemFactory::registerItem(new EnchantingBottle(), true);
		ItemFactory::registerItem(new EnderPearl(), true);
		ItemFactory::registerItem(new Potion(), true);
		ItemFactory::registerItem(new LingeringPotion(), true);
		ItemFactory::registerItem(new SplashPotion(), true);
		ItemFactory::registerItem(new FlintSteel(), true);
		ItemFactory::registerItem(new FireCharge(), true);
		ItemFactory::registerItem(new TotemOfUndying(), true);
		ItemFactory::registerItem(new Elytra(), true);
		ItemFactory::registerItem(new FireworkRocket(), true);
		ItemFactory::registerItem(new ChorusFruit(), true);
		ItemFactory::registerItem(new FishingRod(), true);
		ItemFactory::registerItem(new EyeOfEnder(), true);
		ItemFactory::registerItem(new SpawnEgg(), true);
		ItemFactory::registerItem(new Bow(), true);
		ItemFactory::registerItem(new EndCrystal(), true);
		ItemFactory::registerItem(new Hopper(), true);

		Item::addCreativeItem(Item::get(Item::ENDER_PEARL));
		Item::addCreativeItem(Item::get(Item::BOTTLE_O_ENCHANTING));
		Item::addCreativeItem(Item::get(Item::FIRE_CHARGE));
		Item::addCreativeItem(Item::get(Item::TOTEM));
		Item::addCreativeItem(Item::get(Item::ELYTRA));
		Item::addCreativeItem(Item::get(Item::FIREWORKS));
		Item::addCreativeItem(Item::get(Item::CHORUS_FRUIT));
		Item::addCreativeItem(Item::get(Item::SLIME_BLOCK));
		Item::addCreativeItem(Item::get(Item::ENDER_EYE));
		Item::addCreativeItem(Item::get(Item::END_CRYSTAL));
		Item::addCreativeItem(Item::get(Item::HOPPER));

		for($i = 0; $i <= 36; $i++){
			Item::addCreativeItem(Item::get(Item::SPLASH_POTION, $i));
		}
		/*
		 yep. this loop needs to be repeated.
			   ______
		  .---<__. \ \
		  `---._  \ \ \
		   ,----`- `.))         (Joshua Bell)
		  / ,--.   )  |
		 /_/    >     |
		 |,\__-'      |
		  \_           \
			~~-___      )
				  \      \
		*/
		for($i = 0; $i <= 36; $i++){
			Item::addCreativeItem(Item::get(Item::LINGERING_POTION, $i));
		}

		for($i = 0; $i <= 15; $i++){
			Item::addCreativeItem(Item::get(Item::SHULKER_BOX, $i, 1));
		}
	}
}
