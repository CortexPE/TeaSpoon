<?php

declare(strict_types = 1);

namespace CortexPE;

use CortexPE\block\EndPortal;
use CortexPE\block\Portal;
use pocketmine\block\BlockFactory;
use pocketmine\entity\Entity;
use pocketmine\item\ItemFactory;

class Utils {
	public static function isInsideOfPortal(Entity $entity): bool{
		foreach($entity->getBlocksAround() as $block){
			if($block instanceof Portal){
				return true;
			}
		}

		return false;
	}

	public static function isInsideOfEndPortal(Entity $entity): bool{
		foreach($entity->getBlocksAround() as $block){
			if($block instanceof EndPortal){
				return true;
			}
		}

		return false;
	}

	public static function checkSpoon(){
		return (
			Server::getInstance()->getName() !== "PocketMine-MP" ||
			!class_exists(BlockFactory::class) ||
			!class_exists(ItemFactory::class) ||
			class_exists("pocketmine\\network\\protocol\\Info")
		);
	}
}