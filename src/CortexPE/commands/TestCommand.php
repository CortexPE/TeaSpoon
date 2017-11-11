<?php

/*
 * JUST FOR TESTING STUFF
 */

declare(strict_types = 1);

namespace CortexPE\commands;

use pocketmine\command\{
	CommandSender, defaults\VanillaCommand
};
use pocketmine\level\particle\GenericParticle;
use pocketmine\Player as PMPlayer;

class TestCommand extends VanillaCommand {

	public function __construct($name){
		parent::__construct(
			$name,
			"CortexPE's Command to test stuff before using it",
			"/test"
		);
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if($sender instanceof PMPlayer){
			$sender->getLevel()->addParticle(new GenericParticle($sender->getPosition(), (int)$args[0], (int)$args[1]));
		}
	}
}
