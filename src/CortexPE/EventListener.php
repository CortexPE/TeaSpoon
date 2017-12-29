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

// FYI: Event Priorities work this way: LOWEST -> LOW -> NORMAL -> HIGH -> HIGHEST -> MONITOR -> EXECUTE

namespace CortexPE;

use CortexPE\entity\EndCrystal;
use CortexPE\level\weather\Weather;
use CortexPE\utils\ArmorTypes;
use CortexPE\utils\Xp;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\event\{
	block\BlockBreakEvent, level\LevelLoadEvent, Listener, server\RemoteServerCommandEvent, server\ServerCommandEvent
};
use pocketmine\event\entity\{
	EntityDamageEvent, EntityDeathEvent, EntityTeleportEvent
};
use pocketmine\event\player\{
	PlayerCommandPreprocessEvent, PlayerInteractEvent, PlayerItemHeldEvent, PlayerKickEvent, PlayerLoginEvent, PlayerQuitEvent, PlayerRespawnEvent
};
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\level\Explosion;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\{
	EntityEventPacket, LevelEventPacket
};
use pocketmine\Player;
use pocketmine\Player as PMPlayer;
use pocketmine\plugin\Plugin;
use pocketmine\Server as PMServer;

class EventListener implements Listener {

	const VERSION_COMMANDS = ["version", "ver", "about"];

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
	public function onLevelLoad(LevelLoadEvent $ev){
		if(!Server::$loaded){
			Server::$loaded = true;
			LevelManager::init();
		}

		if(Main::$weatherEnabled){
			$lvl = $ev->getLevel();
			Main::$weatherData[$lvl->getId()] = new Weather($lvl, 0);
			if($lvl->getName() != Main::$netherName && $lvl->getName() != Main::$endName){
				Main::$weatherData[$lvl->getId()]->setCanCalculate(true);
			}else{
				Main::$weatherData[$lvl->getId()]->setCanCalculate(false);
			}
		}else{
			$lvl = $ev->getLevel();
			Main::$weatherData[$lvl->getId()] = new Weather($lvl, 0);
			Main::$weatherData[$lvl->getId()]->setCanCalculate(false);
		}

		return true;
	}

	/**
	 * @param EntityTeleportEvent $ev
	 * @return bool
	 *
	 * @priority HIGHEST
	 */
	/*public function onTeleport(EntityTeleportEvent $ev){
		$p = $ev->getEntity();
		if($p instanceof Player){
			$pk = new ChangeDimensionPacket();
			$pk->dimension = Utils::getDimension($ev->getTo()->getLevel());
			$pk->position = $ev->getTo();
			$p->dataPacket($pk);
		}

		return true;
	}*/

	/**
	 * @param EntityDamageEvent $ev
	 * @return bool
	 *
	 * @priority LOWEST
	 */
	public function onDamage(EntityDamageEvent $ev){
		/////////////////////// TOTEM OF UNDYING ///////////////////////////////
		if($ev->getDamage() >= $ev->getEntity()->getHealth()){
			$p = $ev->getEntity();
			if($p instanceof PMPlayer){
				if($p->getInventory()->getItemInHand()->getId() === Item::TOTEM && $ev->getCause() !== EntityDamageEvent::CAUSE_VOID && $ev->getCause() !== EntityDamageEvent::CAUSE_SUICIDE){
					$ic = clone $p->getInventory()->getItemInHand();
					$ic->count--;
					$p->getInventory()->setItemInHand($ic);
					$ev->setCancelled(true);
					$p->setHealth(1);

					$p->removeAllEffects();

					$effect1 = Effect::getEffect(Effect::REGENERATION);
					$effect2 = Effect::getEffect(Effect::ABSORPTION);
					$effect3 = Effect::getEffect(Effect::FIRE_RESISTANCE);

					$effect1->setAmplifier(1);

					$effect1->setVisible(true);
					$effect2->setVisible(true);
					$effect3->setVisible(true);

					$effect1->setDuration(40 * 20);
					$effect2->setDuration(5 * 20);
					$effect3->setDuration(40 * 20);

					$p->addEffect($effect1);
					$p->addEffect($effect2);
					$p->addEffect($effect3);

					$pk = new LevelEventPacket();
					$pk->evid = LevelEventPacket::EVENT_SOUND_TOTEM;
					$pk->data = 0;
					$pk->position = new Vector3($p->getX(), $p->getY(), $p->getZ());
					$p->dataPacket($pk);

					$pk2 = new EntityEventPacket();
					$pk2->entityRuntimeId = $p->getId();
					$pk2->event = EntityEventPacket::CONSUME_TOTEM;
					$pk2->data = 0;
					$p->dataPacket($pk2);
				}
			}
		}
		////////////////////////////// ARMOR DAMAGE //////////////////////////////////////
		if($ev->getEntity() instanceof Player){
			/** @var Player $p */
			$p = $ev->getEntity();
			$session = Main::getInstance()->getSessionById($p->getId());

			if($session instanceof Session){
				if($ev->getCause() != EntityDamageEvent::CAUSE_LAVA){ // lava damage is handled on the Lava class.
					$session->useArmors();
				}
			}
		}

		/////////////////////// ELYTRA WINGS & SLIME BLOCK ///////////////////////////////
		if($ev->getCause() === EntityDamageEvent::CAUSE_FALL){
			$p = $ev->getEntity();
			if($p instanceof PMPlayer){
				$session = Main::getInstance()->getSessionById($p->getId());
				if($session instanceof Session){
					if($session->usingElytra){
						$ev->setCancelled(true);
					}
					if($p->getLevel()->getBlock($p->subtract(0, 1, 0))->getId() == Block::SLIME_BLOCK){
						$ev->setCancelled(true);
					}
				}
			}
		}

		////////////////////// END CRYSTAL //////////////////
		if($ev->getEntity() instanceof EndCrystal){
			$e = $ev->getEntity();
			$pos = clone $e->getPosition();
			$e->flagForDespawn();
			$explode = new Explosion($pos, 6);
			$explode->explodeA();
			$explode->explodeB();
		}

		return true;
	}

	/**
	 * @param PlayerRespawnEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onRespawn(PlayerRespawnEvent $ev){
		if($ev->getPlayer()->isOnFire()) $ev->getPlayer()->setOnFire(0);
	}

	/**
	 * @param PlayerLoginEvent $ev
	 *
	 * @priority LOWEST
	 */
	public function onLogin(PlayerLoginEvent $ev){
		Main::getInstance()->createSession($ev->getPlayer());
	}

	/**
	 * @param PlayerQuitEvent $ev
	 *
	 * @priority LOWEST
	 */
	public function onLeave(PlayerQuitEvent $ev){
		Main::getInstance()->destroySession($ev->getPlayer());
	}

	/**
	 * @param PlayerKickEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onKick(PlayerKickEvent $ev){
		$p = $ev->getPlayer();
		if(!$p->isOnline()){
			return;
		}
		$pid = $p->getId();
		if($pid === null){
			return;
		}
		$session = Main::getInstance()->getSessionById($pid);
		if($session instanceof Session){
			if($session->isUsingElytra() && $ev->getReason() == PMServer::getInstance()->getLanguage()->translateString("kick.reason.cheat", ["%ability.flight"])){
				$ev->setCancelled(true);
			}
		}
	}

	/**
	 * @param EntityDeathEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onEntityDeath(EntityDeathEvent $ev){
		$xp = Xp::getXpDropsForEntity($ev->getEntity());
		if($xp > 0){
			Xp::spawnXpOrb($ev->getEntity()->getPosition(), $ev->getEntity()->getLevel(), $xp);
		}
	}

	/**
	 * @param BlockBreakEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onBlockBreak(BlockBreakEvent $ev){
		$xp = Xp::getXpDropsForBlock($ev->getBlock());
		if($xp > 0){
			Xp::spawnXpOrb($ev->getBlock(), $ev->getBlock()->getLevel(), $xp);
		}
	}

	/**
	 * @param PlayerCommandPreprocessEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onPlayerCommandPreProcess(PlayerCommandPreprocessEvent $ev){
		if(in_array(substr($ev->getMessage(), 1), self::VERSION_COMMANDS) && !$ev->isCancelled()){
			$ev->setCancelled();
			Main::sendVersion($ev->getPlayer());
		}
	}

	/**
	 * @param ServerCommandEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onServerCommand(ServerCommandEvent $ev){
		if(Utils::in_arrayi($ev->getCommand(), self::VERSION_COMMANDS) && !$ev->isCancelled()){
			$ev->setCancelled();
			Main::sendVersion($ev->getSender());
		}
	}

	/**
	 * @param RemoteServerCommandEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onRemoteServerCommand(RemoteServerCommandEvent $ev){
		if(Utils::in_arrayi($ev->getCommand(), self::VERSION_COMMANDS) && !$ev->isCancelled()){
			$ev->setCancelled();
			Main::sendVersion($ev->getSender());
		}
	}

	/**
	 * @param PlayerItemHeldEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onItemHeld(PlayerItemHeldEvent $ev){
		$session = Main::getInstance()->getSessionById($ev->getPlayer()->getId());
		if($session instanceof Session){
			if($session->fishing){
				if($ev->getSlot() != $session->lastHeldSlot){
					$session->unsetFishing();
				}
			}

			$session->lastHeldSlot = $ev->getSlot();
		}
	}

	/**
	 * @param PlayerInteractEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onInteract(PlayerInteractEvent $ev){
		// MCPE(BE) does this client-side... we just have to do the same server-side.
		$item = $ev->getItem();
		$player = $ev->getPlayer();
		$session = Main::getInstance()->getSessionById($player->getId());

		$check = ($ev->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK || $ev->getAction() == PlayerInteractEvent::RIGHT_CLICK_AIR);

		if($session instanceof Session){
			$controls = $session->clientData["CurrentInputMode"];

			// tnx @Matthww :: https://github.com/Matthww/PlayerInfo/blob/master/src/Matthww/PlayerInfo/PlayerInfo.php

			switch($controls){
				case 1: // Mouse
					// do not modify since this is the default one...
					break;
				case 3: // Controller
					// do not modify since I'm assuming that it works just like a mouse... (I dont have a controller lol)
					break;
				case 0: // Unknown
					// Let's just ASSUME that its controlled by Mouse...
					break;
				case 2: // Touch
					$check = ($ev->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK || $ev->getAction() == PlayerInteractEvent::LEFT_CLICK_AIR || $ev->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK || $ev->getAction() == PlayerInteractEvent::RIGHT_CLICK_AIR);
					break;
			}
		}

		if($check){
			if($ev->getItem() instanceof Armor){
				$inventory = $player->getInventory();
				$type = ArmorTypes::getType($item);
				if($type !== ArmorTypes::TYPE_NULL){
					switch($type){
						case ArmorTypes::TYPE_HELMET:
							$inventory->setHelmet($item);
							break;
						case ArmorTypes::TYPE_CHESTPLATE:
							$inventory->setChestplate($item);
							break;
						case ArmorTypes::TYPE_LEGGINGS:
							$inventory->setLeggings($item);
							break;
						case ArmorTypes::TYPE_BOOTS:
							$inventory->setBoots($item);
							break;
					}
					if($player->isSurvival() || $player->isAdventure()){
						$inventory->setItemInHand(Item::get(Item::AIR, 0, 1));
					}
				}
			}
		}
	}
}
