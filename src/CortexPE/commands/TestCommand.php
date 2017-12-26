<?php

/*
 * JUST FOR TESTING STUFF
 */

declare(strict_types = 1);

namespace CortexPE\commands;

use pocketmine\block\Block;
use pocketmine\command\{
	CommandSender, defaults\VanillaCommand
};
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class TestCommand extends VanillaCommand {

	public function __construct($name){
		parent::__construct(
			$name,
			"CortexPE's Command to test stuff before using it",
			"/test"
		);
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if($sender instanceof Player){
			$pos = new Vector3($sender->getX(), 0, $sender->getZ());
			for($y = 1; $y < Level::Y_MAX; $y++){
				$sender->getLevel()->setBlock($pos->add(0, $y, 0), Block::get(Block::SHULKER_BOX, $y));
			}
		}
	}
}
