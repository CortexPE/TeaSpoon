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


use CortexPE\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Player;
use pocketmine\Server as PMServer;

class BugReportCommand extends VanillaCommand {
	public function __construct($name){
		parent::__construct(
			$name,
			"Dumps parse-able information for Bug / Issue Report"
		);
		$this->setPermission("teaspoon.command.bugreport");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player){
			$sender->sendMessage("This command must be ran using the server's console.");

			return;
		}
		$sender->sendMessage("Dumping Server Information...");
		$str = "";
		$str .= "Server Version: " . $sender->getServer()->getName() . " " . $sender->getServer()->getPocketMineVersion() . "\n";
		$str .= "API Version: " . $sender->getServer()->getApiVersion() . "\n";
		$str .= "Minecraft Version: " . $sender->getServer()->getVersion() . "\n";
		$str .= "Protocol Version: " . ProtocolInfo::CURRENT_PROTOCOL . "\n";
		$str .= "PHP Version: " . PHP_VERSION . "\n";
		$str .= "Host info: " . php_uname("a") . "\n";
		$pstr = "";
		$pstr .= "Plugins: ";
		foreach($sender->getServer()->getPluginManager()->getPlugins() as $pl){
			$pstr .= $pl->getDescription()->getFullName() . ", ";
		}
		$pstr = substr($pstr, 0, -2);
		$str .= $pstr . "\n";
		$str .= "Base64 Encoded Config: " . $this->encodeFile(Main::getInstance()->getDataFolder() . "config.yml") . "\n";
		$str .= "Base64 Encoded PocketMine Configuration: " . $this->encodeFile(PMServer::getInstance()->getDataPath() . "pocketmine.yml") . "\n";
		$str .= "Base64 Encoded Server Properties: " . $this->encodeFile(PMServer::getInstance()->getDataPath() . "server.properties") . "\n";
		$str .= "Base64 Encoded TSP CACHE " . $this->encodeFile(Main::getInstance()->getDataFolder() . "cache.json") . "\n";

		if(!is_dir(Main::getInstance()->getDataFolder() . "dumps")){
			mkdir(Main::getInstance()->getDataFolder() . "dumps");
		}
		$fn = Main::getInstance()->getDataFolder() . "dumps/TeaSpoonDump_" . date("M_j_Y-H.i.s", time()) . ".txt";
		file_put_contents($fn, "TeaSpoon Dump " . date("D M j H:i:s T Y", time()) . "\n", FILE_APPEND);
		file_put_contents($fn, "=== BEGIN BASE64 ENCODED DUMP ===\n", FILE_APPEND);
		file_put_contents($fn, wordwrap(base64_encode($str), 75, "\n", true) . "\n", FILE_APPEND);
		file_put_contents($fn, "=== END OF BASE64 ENCODED DUMP ===", FILE_APPEND);
		$sender->sendMessage("Saved to: " . $fn);
	}

	private function encodeFile(string $filePath): string{
		return base64_encode(file_get_contents($filePath));
	}
}
