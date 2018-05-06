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

use CortexPE\utils\TextFormat;
use pocketmine\command\{
	CommandSender, defaults\VanillaCommand
};
use pocketmine\lang\TranslationContainer;
use pocketmine\Player;

class ClearCommand extends VanillaCommand {

	/**
	 * ClearCommand constructor.
	 *
	 * @param $name
	 */
	public function __construct($name){
		parent::__construct(
			$name,
			"Clears your / another player's inventory",
			"/clear [player]"
		);
		$this->setPermission("pocketmine.command.clear.self;pocketmine.command.clear.other");
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

		if(count($args) >= 2){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

			return false;
		}

		if(count($args) === 1){
			if(!$sender->hasPermission("pocketmine.command.clear.other")){
				$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));

				return true;
			}

			switch($args[0]){
				case '@r':
					$players = $sender->getServer()->getOnlinePlayers();
					if(count($players) > 0){
						$player = $players[array_rand($players)];
					}else{
						$sender->sendMessage("No players online");

						return true;
					}

					if($player instanceof Player){
						$sender->sendMessage("Cleared " . $this->clearTarget($player) . " items from " . $player->getName());
					}

					return true;
				case '@e':
					$sender->sendMessage("Unimplemented since we don't have MobAI yet :/");

					return true;
				case '@p':
					$player = $sender;
					if($player instanceof Player){
						$this->clearTarget($player);
					}else{
						$sender->sendMessage("You must run this command in-game");
					}

					return true;
				default;
					$player = $sender->getServer()->getPlayer($args[0]);
					if($player instanceof Player){
						$sender->sendMessage("Cleared " . $this->clearTarget($player) . " items from " . $player->getName());
					}else{
						$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
					}

					return true;
			}
		}

		if($sender instanceof Player){
			if(!$sender->hasPermission("pocketmine.command.clear.self")){
				$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));

				return true;
			}

			$sender->sendMessage("Cleared " . $this->clearTarget($sender) . " items from " . $sender->getName());
		}else{
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

			return false;
		}

		return true;
	}

	private function clearTarget(Player $p): int{
		$count = 0;
		$items = $p->getInventory()->getContents() + $p->getArmorInventory()->getContents();
		foreach($items as $item){
			$count += $item->getCount();
		}
		$p->getInventory()->clearAll();
		$p->getArmorInventory()->clearAll();

		return $count;
	}
}
