<?php

declare(strict_types=1);

namespace CortexPE\task;

use CortexPE\Main;
use CortexPE\Utils;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\Server;
use pocketmine\scheduler\PluginTask;

class CheckPlayersTask extends PluginTask {
	public function onRun(int $currentTick){
		foreach(Server::getInstance()->getOnlinePlayers() as $p){
			$epo = Utils::isInsideOfEndPortal($p);
			$po = Utils::isInsideOfPortal($p);
			if($epo || $po && !in_array($p->getName(), Main::$teleporting)){
				if($p->getLevel()->getName() !== Main::$netherLevel->getName() && $p->getLevel()->getName() !== Main::$endLevel->getName()){
					if($po){
						$pk = new ChangeDimensionPacket();
						$pk->dimension = DimensionIds::NETHER;
						$pk->position = Main::$netherLevel->getSafeSpawn();
						$p->teleport(Main::$netherLevel->getSafeSpawn());
						//$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
						Main::$teleporting[] = $p->getName();
					} else if($epo){
						$pk = new ChangeDimensionPacket();
						$pk->dimension = DimensionIds::THE_END;
						$pk->position = Main::$endLevel->getSafeSpawn();
						$p->teleport(Main::$endLevel->getSafeSpawn());
						//$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
						Main::$teleporting[] = $p->getName();
					}
				} else {
					$pk = new ChangeDimensionPacket();
					$pk->dimension = DimensionIds::OVERWORLD;
					$pk->position = Server::getInstance()->getDefaultLevel()->getSafeSpawn();
					$p->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
					//$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
					Main::$teleporting[] = $p->getName();
				}
			}
		}
	}
}