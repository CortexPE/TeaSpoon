<?php

/*
 * JUST FOR TESTING STUFF
 */

declare(strict_types = 1);

namespace CortexPE\commands;

use pocketmine\command\{CommandSender, defaults\VanillaCommand};

class TestCommand extends VanillaCommand {

	public function __construct($name){
		parent::__construct(
			$name,
			"CortexPE's Command to test stuff before using it",
			"/test"
		);
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){

	}
}
