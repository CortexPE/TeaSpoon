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
 * @link http://CortexPE.xyz
 *
 */

declare(strict_types = 1);

// FYI: Event Priorities work this way: LOWEST -> LOW -> NORMAL -> HIGH -> HIGHEST -> MONITOR

namespace CortexPE;

use CortexPE\entity\projectile\EnderPearl;
use CortexPE\item\enchantment\Enchantment;
use CortexPE\task\DelayedTeleportTask;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\plugin\Plugin;
use pocketmine\Server as PMServer;
use pocketmine\Player as PMPlayer;

class EventListener implements Listener {

	/** @var Plugin */
	public $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
	}

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
			PMServer::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedTeleportTask($this->plugin, $ev->getPlayer()), 20);
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
	public function onDamage(EntityDamageEvent $ev){
		if($ev->getDamage() >= $ev->getEntity()->getHealth()){
			$p = $ev->getEntity();
			if($p instanceof PMPlayer){
				if($p->getInventory()->getItemInHand()->getId() === Item::TOTEM && $ev->getCause() !== EntityDamageEvent::CAUSE_VOID && $ev->getCause() !== EntityDamageEvent::CAUSE_SUICIDE){
					$ic = clone $p->getInventory()->getItemInHand();
					$ic->count--;
					$p->getInventory()->setItemInHand($ic);
					$ev->setCancelled(true);
					$p->setHealth(2);

					$pk = new LevelEventPacket();
					$pk->evid = LevelEventPacket::EVENT_SOUND_TOTEM;
					$pk->data = 0;
					$pk->position = new Vector3($p->getX(), $p->getY(), $p->getZ());
					$p->dataPacket($pk);

					$pk2 = new LevelEventPacket();
					$pk2->evid = 2005;
					$pk2->data = 0;
					$pk2->position = new Vector3($p->getX(), $p->getY(), $p->getZ());
					$p->dataPacket($pk2);

					// TODO: Find the LevelEventID for Totem Animation (If it exists... I'm guessing it does.)
				}
			}
		}
		if($ev instanceof EntityDamageByEntityEvent){ // TODO: ADD MORE ENCHANTS
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
										$d->attack(new EntityDamageEvent($e, EntityDamageEvent::CAUSE_ENTITY_ATTACK, mt_rand($enchantment->getLevel(), 3 + $enchantment->getLevel())));
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

	/**
	 * @param PlayerRespawnEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onRespawn(PlayerRespawnEvent $ev){ // Other plugins might cancel it. so...
		if($ev->getPlayer()->isOnFire())$ev->getPlayer()->setOnFire(0);
	}

	/**
	 * @param PlayerLoginEvent $ev
	 *
	 * @priority LOWEST
	 */
	public function onLogin(PlayerLoginEvent $ev){
		Main::$lastUses[$ev->getPlayer()->getName()] = 0;
	}

	/**
	 * @param ProjectileLaunchEvent $ev
	 *
	 * @priority LOWEST
	 */
	public function onEnderPearlUse(ProjectileLaunchEvent $ev){
		if($ev->getEntity() instanceof EnderPearl){
			$e = $ev->getEntity();
			$p = $e->getOwningEntity();
			if($p instanceof PMPlayer){
				if(floor(microtime(true) - Main::$lastUses[$p->getName()]) < Main::$enderPearlCooldown){
					$ev->setCancelled(true);
					$e->close();
				} else {
					Main::$lastUses[$p->getName()] = time();
				}
			}
		}
	}
}