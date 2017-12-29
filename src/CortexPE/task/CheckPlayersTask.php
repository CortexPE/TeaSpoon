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

namespace CortexPE\task;

use CortexPE\{
	Main, Session, Utils
};
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;

class CheckPlayersTask extends PluginTask {

	public function onRun(int $currentTick){
		foreach(Server::getInstance()->getOnlinePlayers() as $p){
			$session = Main::getInstance()->getSessionById($p->getId());
			if($session instanceof Session){
				if($session->skipCheck){
					continue;
				}
				if(Main::$registerDimensions){
					$epo = Utils::isInsideOfEndPortal($p);
					$po = Utils::isInsideOfPortal($p);
					if($epo || $po && Main::$registerDimensions){
						if($p->getLevel()->getSafeSpawn()->distance($p) <= 0.1){
							return; // It's Probably a PMMP Teleport Bug Causing it. Short desc: $player->getBlocksAround() doesnt update on teleport... it only updates again on move.
						}
						if($p->getLevel()->getName() !== Main::$netherLevel->getName() && $p->getLevel()->getName() !== Main::$endLevel->getName()){
							if($po){
								$this->scheduleTeleport($p, DimensionIds::NETHER, Main::$netherLevel->getSafeSpawn(), true);
							}elseif($epo){
								$this->scheduleTeleport($p, DimensionIds::THE_END, Main::$endLevel->getSafeSpawn());
							}
						}else{
							$this->scheduleTeleport($p, DimensionIds::OVERWORLD, Server::getInstance()->getDefaultLevel()->getSafeSpawn(), $po);
						}
					}

					if($p->isOnFire()){
						if(Main::$weatherEnabled){
							$weather = Main::$weatherData[$p->getLevel()->getId()];
							$rainy = $weather->isRainy() || $weather->isRainyThunder();
							if(Utils::canSeeSky($p->getLevel(), $p) && $rainy){
								$p->setOnFire(0);
							}
						}
					}
				}
				/*if($p->getInventory()->getBoots()->hasEnchantment(Enchantment::FROST_WALKER)){
					 $ench = $p->getInventory()->getBoots()->getEnchantment(Enchantment::FROST_WALKER);
					LAGGY
					if($ench->getLevel() < 1){
						continue;
					}
					if($ench->getLevel() > 1){
						$radius = 5;
					} else {
						$radius = 3;
					}
					for ($a = -$radius; $a <= $radius; $a++) {
						for ($c = -$radius; $c <= $radius; $c++) {
							if ($a * $a + $c * $c <= $radius * $radius) {
								$lvl = $p->getLevel();
								$pos = new Vector3($p->getX() + $a, $p->getY() - 1, $p->getZ() + $c);
								if(in_array($lvl->getBlock($pos)->getId(), [Block::WATER, Block::STILL_WATER])){
									$lvl->setBlock($pos, new FrostedIce(), true, true);
								}
							}
						}
					}
			}*/
				if(Main::$debug){
					$lVec = $p->getTargetBlock(10);
					$p->sendTip("Looking At: [$lVec->x, $lVec->y, $lVec->z] - " . $lVec->getName() . "(" . $lVec->getId() . "):" . $lVec->getDamage() . "\nTPS: " . Server::getInstance()->getTicksPerSecond());
				}
			}
		}
	}

	private function scheduleTeleport(Player $player, int $dimension, Vector3 $pos, bool $toAndFromNether = false){
		if($toAndFromNether){
			Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask($this->owner, $player, $dimension, $pos), 4 * 20);
		}else{
			Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask($this->owner, $player, $dimension, $pos), 1);
		}
		Main::getInstance()->getSessionById($player->getId())->skipCheck = true;
	}
}
