<?php

/*
 *     __                           _
 *    / /  _____   _____ _ __ _   _| |
 *   / /  / _ \ \ / / _ \ '__| | | | |
 *  / /__|  __/\ V /  __/ |  | |_| | |
 *  \____/\___| \_/ \___|_|   \__, |_|
 *                            |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author LeverylTeam
 * @link https://github.com/LeverylTeam
 *
*/

declare(strict_types = 1);

namespace CortexPE\commands;

use pocketmine\utils\TextFormat;
use pocketmine\command\{
	CommandSender, defaults\VanillaCommand
};
use pocketmine\Player;

class WorldCommand extends VanillaCommand {

	public function __construct($name){
		parent::__construct(
			$name,
			"Teleportiert dich zu einer Welt",
			"/world [target player] <world name>"
		);
		$this->setPermission("pocketmine.command.world");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if($sender instanceof Player){
			if(count($args) == 1){
				$sender->getServer()->loadLevel($args[0]);
				if(($level = $sender->getServer()->getLevelByName($args[0])) !== null){
					$sender->teleport($level->getSafeSpawn());
					$sender->sendMessage("Du wurdest teleportiert zu: " . $level->getName());

					return true;
				}else{
					$sender->sendMessage(TextFormat::RED . "Die Welt: \"" . $args[0] . "\" ist nicht vorhanden");

					return false;
				}
			}elseif(count($args) > 1 && count($args) < 3){
				$sender->getServer()->loadLevel($args[1]);
				if(($level = $sender->getServer()->getLevelByName($args[1])) !== null){
					$player = $sender->getServer()->getPlayer($args[0]);
					if($player === null){
						$sender->sendMessage("Spieler nicht gefunden.");

						return false;
					}
					$player->teleport($level->getSafeSpawn());
					$player->sendMessage("Du wurdest teleportiert zu: " . $level->getName());

					return true;
				}else{
					$sender->sendMessage(TextFormat::RED . "Die Welt: \"" . $args[1] . "\" ist nicht vorhanden");

					return false;
				}
			}else{
				$sender->sendMessage("Benutze: /world [Spielername] <world name>");

				return false;
			}
		}else{
			$sender->sendMessage(TextFormat::RED . "Dieser Command kann nur an einem Spieler ausgeführt werden!");

			return false;
		}
	}
}
