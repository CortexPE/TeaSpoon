<?php

namespace CortexPE\block;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

class BlockManager {
	public static function init(){
		self::register(Block::PORTAL, new Portal());
		self::register(Block::END_PORTAL, new EndPortal());
		self::register(Block::ENDER_CHEST, new EnderChest());
		self::register(Block::OBSIDIAN, new Obsidian(), true);
	}

	public static function register(int $id, Block $block, bool $overwrite = false): bool{
		if(!BlockFactory::isRegistered($id) && !$overwrite){
			BlockFactory::registerBlock($block);

			return true;
		}elseif($overwrite){
			BlockFactory::registerBlock($block, true);

			return true;
		}

		return false;
	}
}