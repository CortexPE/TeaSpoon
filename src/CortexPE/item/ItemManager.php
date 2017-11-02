<?php

declare(strict_types = 1);

namespace CortexPE\item;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class ItemManager {
	public static function init(){
		ItemFactory::registerItem(new EnchantingBottle());
		ItemFactory::registerItem(new EnderPearl());
		ItemFactory::registerItem(new Potion(), true);
		ItemFactory::registerItem(new SplashPotion());
		ItemFactory::registerItem(new FlintSteel(), true);
		ItemFactory::registerItem(new FireCharge());
		ItemFactory::registerItem(new TotemOfUndying());

		Item::addCreativeItem(Item::get(Item::ENDER_PEARL));
		Item::addCreativeItem(Item::get(Item::ENDER_CHEST));
		Item::addCreativeItem(Item::get(Item::BOTTLE_O_ENCHANTING));
		Item::addCreativeItem(Item::get(Item::FIRE_CHARGE));
		Item::addCreativeItem(Item::get(Item::TOTEM));

		for($i = 0; $i <= 36; $i++){
			Item::addCreativeItem(Item::get(Item::SPLASH_POTION, $i));
		}
	}
}