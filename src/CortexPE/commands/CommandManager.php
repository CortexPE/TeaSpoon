<?php

namespace CortexPE\commands;

use pocketmine\Server as PMServer;

class CommandManager {
	public static function init(){
		PMServer::getInstance()->getCommandMap()->registerAll("pocketmine", [
			new WorldCommand("world"),
		]);
	}
}