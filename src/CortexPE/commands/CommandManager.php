<?php

namespace CortexPE\commands;

use pocketmine\command\Command;
use pocketmine\Server as PMServer;

class CommandManager {
	public static function init(){
		PMServer::getInstance()->getCommandMap()->registerAll("pocketmine", [
			new WorldCommand("world"),
			//new TestCommand("test"), // COMMENT THIS OUT. ALWAYS.
		]);

		self::overwrite(new KillCommand("kill"), "kill");
	}

	public static function overwrite(Command $cmd, string $commandName){
		// Thank you very much iksaku for leaving this method on the *good o'l* PocketMine Forums. :)
		$cmdMap = PMServer::getInstance()->getCommandMap();
		$cmdOverwrite = $cmdMap->getCommand($commandName);
		$cmdOverwrite->setLabel($cmdOverwrite->getLabel() . "__disabled");
		$cmdMap->unregister($cmdOverwrite);

		$cmdMap->register("pocketmine", $cmd);
	}
}