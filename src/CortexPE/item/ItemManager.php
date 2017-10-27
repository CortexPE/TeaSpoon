<?php

declare(strict_types=1);

namespace CortexPE\item;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class ItemManager {
	public static function init(){
		ItemFactory::registerItem(new EnderPearl());

		Item::addCreativeItem(Item::get(Item::ENDER_PEARL));
		Item::addCreativeItem(Item::get(Item::ENDER_CHEST));
	}
}