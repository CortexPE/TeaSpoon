<?php

namespace CortexPE\block;

use pocketmine\block\BlockFactory;

class BlockManager {
	public static function init(){
		BlockFactory::registerBlock(new Portal());
		BlockFactory::registerBlock(new EndPortal());
		BlockFactory::registerBlock(new EnderChest());
	}
}