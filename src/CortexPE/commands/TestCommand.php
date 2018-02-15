<?php

/*
 * JUST FOR TESTING STUFF
 */

declare(strict_types = 1);

namespace CortexPE\commands;

use pocketmine\command\{
	CommandSender, defaults\VanillaCommand
};
use pocketmine\Player;
use pocketmine\tile\Tile;

class TestCommand extends VanillaCommand {

	public function __construct($name){
		parent::__construct(
			$name,
			"CortexPE's Command to test stuff",
			"/test"
		);
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if($sender instanceof Player){
			$block = $sender->getTargetBlock(10);
			$tile = $sender->getLevel()->getTile($block);
			$sender->sendMessage("Block: " . $block->getName());
			$sender->sendMessage("Tile: " . ($tile instanceof Tile ? get_class($tile) : "null"));
		}
	}
}
