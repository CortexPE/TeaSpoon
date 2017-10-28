<?php

// FYI: Event Priorities work this way: LOWEST -> LOW -> NORMAL -> HIGH -> HIGHEST -> MONITOR

declare(strict_types = 1);

namespace CortexPE;

use CortexPE\item\enchantment\Enchantment;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
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
	public function onPostLevelLoad(/** @noinspection PhpUnusedParameterInspection */
		LevelLoadEvent $ev){
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
		if(Main::$checkingMode == "event"/* && !in_array($p->getName(), Main::$teleporting)*/){
			$epo = Utils::isInsideOfEndPortal($p);
			$po = Utils::isInsideOfPortal($p);
			if($epo || $po/* && !in_array($p->getName(), Main::$teleporting)*/){
				if($p->getLevel()->getName() !== Main::$netherLevel->getName() && $p->getLevel()->getName() !== Main::$endLevel->getName()){
					if($po){
						$pk = new ChangeDimensionPacket();
						$pk->dimension = DimensionIds::NETHER;
						$pk->position = Main::$netherLevel->getSafeSpawn();
						$p->dataPacket($pk);
						$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
						$p->teleport(Main::$netherLevel->getSafeSpawn());
						//Main::$teleporting[] = $p->getName();
					}elseif($epo){
						$pk = new ChangeDimensionPacket();
						$pk->dimension = DimensionIds::THE_END;
						$pk->position = Main::$endLevel->getSafeSpawn();
						$p->dataPacket($pk);
						$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
						$p->teleport(Main::$endLevel->getSafeSpawn());
						//Main::$teleporting[] = $p->getName();
					}
				}else{
					$pk = new ChangeDimensionPacket();
					$pk->dimension = DimensionIds::OVERWORLD;
					$pk->position = Server::getInstance()->getDefaultLevel()->getSafeSpawn();
					$p->dataPacket($pk);
					$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
					$p->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
					//Main::$teleporting[] = $p->getName();
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
	/*public function onJoin(PlayerJoinEvent $ev){ TODO: Fix this.
		$p = $ev->getPlayer();
		if($p->getLevel()->getName() === Main::$netherLevel->getName()){
			$pk = new ChangeDimensionPacket();
			$pk->dimension = DimensionIds::NETHER;
			$pk->position = new Position($p->getX(), $p->getY(), $p->getZ(), $p->getLevel());
			$p->dataPacket($pk);
			$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
			//$p->teleport($ev->getPlayer()->getPosition());
			//$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
		}
		if($p->getLevel()->getName() === Main::$endLevel->getName()){
			$pk = new ChangeDimensionPacket();
			$pk->dimension = DimensionIds::THE_END;
			$pk->position = new Position($p->getX(), $p->getY(), $p->getZ(), $p->getLevel());
			$p->dataPacket($pk);
			$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
			//$p->teleport($ev->getPlayer()->getPosition());
			//$p->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
		}
		return true;
	}*/


	/**
	 * @param PlayerDeathEvent $ev
	 * @return bool
	 *
	 * @priority HIGHEST
	 */
	public function onDeath(PlayerDeathEvent $ev){
		if($ev->getPlayer()->getLevel()->getName() === Main::$netherLevel->getName() || $ev->getPlayer()->getLevel()->getName() === Main::$endLevel->getName()){
			//$ev->getPlayer()->setSpawn(PMServer::getInstance()->getDefaultLevel()->getSafeSpawn()); // So that dying isn't a loop on other dimensions
			$pk = new ChangeDimensionPacket();
			$pk->dimension = DimensionIds::OVERWORLD;
			$pk->position = PMServer::getInstance()->getDefaultLevel()->getSafeSpawn();
			$pk->respawn = true;
			$ev->getPlayer()->dataPacket($pk);
			//$ev->getPlayer()->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
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
		if($p instanceof Player/* && !in_array($p->getName(), Main::$teleporting)*/){
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
		}/* else if(in_array($p->getName(), Main::$teleporting)){
			unset(Main::$teleporting[array_search($p->getName(), Main::$teleporting)]);
		}*/

		return true;
	}

	/**
	 * @param EntityDamageEvent $ev
	 * @return bool
	 *
	 * @priority LOWEST
	 */
	public function onDamage(EntityDamageEvent $ev){ // TODO: ADD MORE ENCHANTS
		if($ev instanceof EntityDamageByEntityEvent){
			$e = $ev->getEntity();
			$d = $ev->getDamager();

			if($d instanceof Player && $e instanceof Living){
				$i = $d->getInventory()->getItemInHand();
				if($i->hasEnchantments()){
					foreach($i->getEnchantments() as $ench){
						switch($ench->getId()){
							case Enchantment::FIRE_ASPECT:
								$e->setOnFire(($ench->getLevel() * 4) * 20); // #BlamePMMP // Fire doesnt last for less than half a second. wtf.
								break;
							case Enchantment::KNOCKBACK:
								$ev->setKnockBack($ev->getKnockBack() + ($ench->getLevel() * 1.5));
								break;
						}
					}
				}
				if($e instanceof Player){
					foreach($e->getInventory()->getArmorContents() as $armorContent){
						if($armorContent->hasEnchantments()){
							foreach($armorContent->getEnchantments() as $enchantment){
								switch($enchantment->getId()){
									case Enchantment::THORNS:
										$d->attack(new EntityDamageEvent($e, EntityDamageEvent::CAUSE_ENTITY_ATTACK, mt_rand($enchantment->getLevel(),3 + $enchantment->getLevel())));
										break;
								}
							}
						}
					}
				}
			}
		}

		return true;
	}
}