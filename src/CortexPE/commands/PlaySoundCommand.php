<?php

/**
 *
 * MMP""MM""YMM               .M"""bgd
 * P'   MM   `7              ,MI    "Y
 *      MM  .gP"Ya   ,6"Yb.  `MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.
 *      MM ,M'   Yb 8)   MM    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM
 *      MM 8M""""""  ,pm9MM  .     `MM MM    M8 8M     M8 8M     M8 MM    MM
 *      MM YM.    , 8M   MM  Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM
 *    .JMML.`Mbmmd' `Moo9^Yo.P"Ybmmd"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML.
 *                                     MM
 *                                   .JMML.
 * This file is part of TeaSpoon.
 *
 * TeaSpoon is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeaSpoon is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TeaSpoon.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author CortexPE
 * @link https://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE\commands;

use pocketmine\command\{
	CommandSender, defaults\VanillaCommand
};
use pocketmine\lang\TranslationContainer;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\Server;

class PlaySoundCommand extends VanillaCommand {
	public function __construct($name){
		parent::__construct(
			$name,
			"Plays a sound",
			"/playsound <sound> <player> [x] [y] [z] [volume] [pitch]"
		);
		$this->setPermission("pocketmine.command.playsound");
	}

	/**
	 * @param CommandSender $sender
	 * @param string $currentAlias
	 * @param array $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(!isset($args[0]) || !isset($args[1])){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

			return false;
		}

		$server = Server::getInstance();
		$player = $server->getPlayer($args[1]);

		if($player instanceof Player === false){
			$sender->sendMessage("Cannot find Player.");

			return false;
		}

		$sound = $args[0] ?? "";
		$x = $args[2] ?? $player->getX();
		$y = $args[3] ?? $player->getY();
		$z = $args[4] ?? $player->getZ();
		$volume = $args[5] ?? 500;
		$pitch = $args[6] ?? 1;

		$pk = new PlaySoundPacket();
		$pk->soundName = $sound;
		$pk->x = $x;
		$pk->y = $y;
		$pk->z = $z;
		$pk->volume = $volume;
		$pk->pitch = $pitch;

		$server->broadcastPacket($player->getLevel()->getPlayers(), $pk);
		$sender->sendMessage("Playing " . $sound . " to " . $player->getName());

		return true;
	}
}
