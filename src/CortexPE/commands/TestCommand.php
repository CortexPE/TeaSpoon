<?php

/*
 * JUST FOR TESTING STUFF
 */

declare(strict_types = 1);

namespace CortexPE\commands;

use CortexPE\Utils;
use pocketmine\command\{
	CommandSender, defaults\VanillaCommand
};

class TestCommand extends VanillaCommand {

	public function __construct($name){
		parent::__construct(
			$name,
			"CortexPE's Command to test stuff before using it",
			"/test"
		);
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		$sender->sendMessage("-==[ :: Ze Epic NOT Gate test :: ]==-");
		$sender->sendMessage("TEST 1");
		if(true){
			$res = "false";
		} else {
			$res = true;
		}
		$sender->sendMessage("true -> " . $res);
		if(false){
			$res = "false";
		} else {
			$res = true;
		}
		$sender->sendMessage("false -> " . $res);

		$sender->sendMessage("TEST 2");
		$res = true ? "false" : "true";
		$sender->sendMessage("true -> " . $res);
		$res = false ? "false" : "true";
		$sender->sendMessage("false -> " . $res);

		$sender->sendMessage("TEST 3");
		$res = !true ? "true" : "false";
		$sender->sendMessage("true -> " . $res);
		$res = !false ? "true" : "false";
		$sender->sendMessage("false -> " . $res);

		$sender->sendMessage("TEST 4");
		$res = Utils::boolToString(!true);
		$sender->sendMessage("true -> " . $res);
		$res = Utils::boolToString(!false);
		$sender->sendMessage("false -> " . $res);
	}
}
