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

use CortexPE\Main;
use pocketmine\item\{
	Item, ItemFactory
};

class ItemManager {
	public static function init(){
		ItemFactory::registerItem(new Boat(), true);
		ItemFactory::registerItem(new LingeringPotion(), true);
		ItemFactory::registerItem(new FlintSteel(), true);
		ItemFactory::registerItem(new FireCharge(), true);
		ItemFactory::registerItem(new Elytra(), true);
		ItemFactory::registerItem(new Fireworks(), true);
		ItemFactory::registerItem(new FishingRod(), true);
		ItemFactory::registerItem(new EyeOfEnder(), true);
		ItemFactory::registerItem(new SpawnEgg(), true);
		ItemFactory::registerItem(new Bow(), true);
		ItemFactory::registerItem(new EndCrystal(), true);
		ItemFactory::registerItem(new Bucket(), true);
		ItemFactory::registerItem(new ArmorStand(), true);
		if(Main::$cars){
			ItemFactory::registerItem(new Minecart(), true);
		}
		//ItemFactory::registerItem(new Lead(), true);
		ItemFactory::registerItem(new BlazeRod(), true);
		ItemFactory::registerItem(new DragonBreath(), true);
		ItemFactory::registerItem(new GlassBottle(), true);
		//ItemFactory::registerItem(new Trident(), true);

		//ItemFactory::registerItem(new Record(Item::RECORD_13, 0, "Music Disc 13"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_CAT, 0, "Music Disc cat"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_BLOCKS, 0, "Music Disc blocks"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_CHIRP, 0, "Music Disc chirp"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_FAR, 0, "Music Disc far"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_MALL, 0, "Music Disc mall"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_MELLOHI, 0, "Music Disc mellohi"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_STAL, 0, "Music Disc stal"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_STRAD, 0, "Music Disc strad"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_WARD, 0, "Music Disc ward"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_11, 0, "Music Disc 11"), true);
		//ItemFactory::registerItem(new Record(Item::RECORD_WAIT, 0, "Music Disc wait"), true);

		Item::initCreativeItems();
	}
}
