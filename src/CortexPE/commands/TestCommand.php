<?php

/*
 * JUST FOR TESTING STUFF
 */

declare(strict_types = 1);

namespace CortexPE\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
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
			$pk = new LevelEventPacket();
			$pk->evid = (int) $args[0] ?? LevelEventPacket::EVENT_SOUND_TOTEM; // 2005,
			$pk->data = 0; // idk.
			$pk->position = new Vector3($sender->getX(), $sender->getY(), $sender->getZ());
			$sender->dataPacket($pk);
		}
	}
}
