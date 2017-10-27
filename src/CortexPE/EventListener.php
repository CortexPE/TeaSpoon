<?php

// FYI: Event Priorities work this way: LOWEST -> LOW -> NORMAL -> HIGH -> HIGHEST -> MONITOR

declare(strict_types=1);

namespace CortexPE;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\Server as PMServer;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\types\DimensionIds;

class EventListener implements Listener {
	/**
	 * @param LevelLoadEvent $ev
	 * @return bool
	 *
	 * @priority LOWEST
	 */
	public function onPostLevelLoad(/** @noinspection PhpUnusedParameterInspection */LevelLoadEvent $ev){
		if(!Server::$loaded){
			Server::$loaded = true;
			LevelManager::init();
		}
		return true;
	}

	/**
	 * @param PlayerMoveEvent $ev
	 * @return bool
	 *
	 * @priority HIGHEST
	 */
	public function onPlayerMove(PlayerMoveEvent $ev){
		$p = $ev->getPlayer();
		if(Main::$checkingMode == "event" && !in_array($p->getName(), Main::$teleporting)){
			$epo = Utils::isInsideOfEndPortal($p);
			$po = Utils::isInsideOfPortal($p);
			if($epo || $po){
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
		return false;
	}

	/**
	 * @param PlayerJoinEvent $ev
	 * @return bool
	 *
	 * @priority HIGHEST
	 */
	public function onJoin(PlayerJoinEvent $ev){
		$p = $ev->getPlayer();
		if($p === Main::$netherLevel){
			$pk = new ChangeDimensionPacket();
			$pk->dimension = DimensionIds::NETHER;
			$pk->position = $ev->getPlayer()->getPosition();
			$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
		}
		if($p === Main::$endLevel){
			$pk = new ChangeDimensionPacket();
			$pk->dimension = DimensionIds::THE_END;
			$pk->position = $ev->getPlayer()->getPosition();
			$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
		}
		return true;
	}


	/**
	 * @param PlayerDeathEvent $ev
	 * @return bool
	 *
	 * @priority HIGHEST
	 */
	public function onDeath(PlayerDeathEvent $ev){
		if($ev->getPlayer()->getLevel()->getName() === Main::$netherLevel->getName() || $ev->getPlayer()->getLevel()->getName() === Main::$endLevel->getName()){
			$ev->getPlayer()->setSpawn(PMServer::getInstance()->getDefaultLevel()->getSafeSpawn()); // So that dying isn't a loop on other dimensions
			$ev->getPlayer()->teleport(PMServer::getInstance()->getDefaultLevel()->getSafeSpawn());
		}
		return true;
	}


	/**
	 * @param EntityTeleportEvent $ev
	 * @return bool
	 *
	 * @priority HIGHEST
	 */
	public function onTeleport(EntityTeleportEvent $ev){
		$p = $ev->getEntity();
		if($p instanceof Player && !in_array($p->getName(), Main::$teleporting)){
			switch($ev->getTo()->getLevel()->getName()){
				case Main::$netherLevel->getName():
					$pk = new ChangeDimensionPacket();
					$pk->dimension = DimensionIds::NETHER;
					$pk->position = $ev->getTo();
					$p->dataPacket($pk);
					break;
				case Main::$endLevel->getName():
					$pk = new ChangeDimensionPacket();
					$pk->dimension = DimensionIds::THE_END;
					$pk->position = $ev->getTo();
					$p->dataPacket($pk);
					break;
				default:
					$pk = new ChangeDimensionPacket();
					$pk->dimension = DimensionIds::OVERWORLD;
					$pk->position = $ev->getTo();
					$p->dataPacket($pk);
					break;
			}
		} else if(in_array($p->getName(), Main::$teleporting)){
			unset(Main::$teleporting[array_search($p->getName(), Main::$teleporting)]);
		}
		return true;
	}

	/**
	 * @param EntityDamageEvent $ev
	 * @return bool
	 *
	 * @priority LOWEST
	 */
	public function onDamage(EntityDamageEvent $ev){
		// TODO: Add working Enchants here...
		return true;
	}
}