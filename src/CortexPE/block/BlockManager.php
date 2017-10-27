<?php

namespace CortexPE\block;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

class BlockManager {
	public static function init(){
		self::register(Block::PORTAL, new Portal());
		self::register(Block::END_PORTAL, new EndPortal());
		self::register(Block::END_PORTAL, new EnderChest());
	}

	public static function register(int $id, Block $block, bool $overwrite = false) : bool {
		if(!BlockFactory::isRegistered($id)){
			BlockFactory::registerBlock($block, $overwrite);
			return true;
		}
		return false;
	}
}