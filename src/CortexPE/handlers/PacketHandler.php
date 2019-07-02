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

namespace CortexPE\handlers;

use CortexPE\EventListener;
use CortexPE\Main;
use CortexPE\network\InventoryTransactionPacket;
use CortexPE\Session;
use CortexPE\Utils;
use pocketmine\entity\Villager;
use pocketmine\event\{
	Listener, server\DataPacketReceiveEvent, server\DataPacketSendEvent
};
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\{
	PlayerActionPacket, StartGamePacket
};
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\Player as PMPlayer;
use pocketmine\plugin\Plugin;

class PacketHandler implements Listener {

	/** @var Plugin */
	public $plugin;

    /**
     * @var EventListener
     */
	public $eventListener;

	public function __construct(Plugin $plugin, EventListener $eventListener){
		$this->plugin = $plugin;
		$this->eventListener = $eventListener;
	}

	/**
	 * @param DataPacketReceiveEvent $ev
	 *
	 * @priority LOWEST
	 */
	public function onPacketReceive(DataPacketReceiveEvent $ev){
		$pk = $ev->getPacket();
		$p = $ev->getPlayer();

		switch(true){
			case ($pk instanceof PlayerActionPacket):
				$session = Main::getInstance()->getSessionById($p->getId());
				if($session instanceof Session){
					switch($pk->action){
						case PlayerActionPacket::ACTION_DIMENSION_CHANGE_ACK:
						case PlayerActionPacket::ACTION_DIMENSION_CHANGE_REQUEST:
							$pk->action = PlayerActionPacket::ACTION_RESPAWN; // redirect to respawn action so that PMMP would handle it as a respawn
							break;

						case PlayerActionPacket::ACTION_START_GLIDE:
							if(Main::$elytraEnabled){
								$p->setGenericFlag(PMPlayer::DATA_FLAG_GLIDING, true);

								$session->usingElytra = $session->allowCheats = true;
							}
							break;
						case PlayerActionPacket::ACTION_STOP_GLIDE:
							if(Main::$elytraEnabled){
								$p->setGenericFlag(PMPlayer::DATA_FLAG_GLIDING, false);

								$session->usingElytra = $session->allowCheats = false;

								$session->damageElytra();
							}
							break;
						case PlayerActionPacket::ACTION_START_SWIMMING:
							$p->setGenericFlag(PMPlayer::DATA_FLAG_SWIMMING, true);
							break;
						case PlayerActionPacket::ACTION_STOP_SWIMMING:
							$p->setGenericFlag(PMPlayer::DATA_FLAG_SWIMMING, false);
							break;
					}
				}
				break;
			case ($pk instanceof InventoryTransactionPacket): // TODO: Remove this once https://github.com/pmmp/PocketMine-MP/pull/2124 gets merged
				if($pk->transactionType == InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY){
					if($pk->trData->actionType == InventoryTransactionPacket::USE_ITEM_ON_ENTITY_ACTION_INTERACT){
						$entity = $p->getLevel()->getEntity($pk->trData->entityRuntimeId);
						$item = $p->getInventory()->getItemInHand();
						$slot = $pk->trData->hotbarSlot;
						$clickPos = $pk->trData->clickPos;
                        if($pk->transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY && isset($pk->trData->entityRuntimeId)){
                            $entity = $p->level->getEntity($pk->trData->entityRuntimeId);
                            if($entity instanceof Villager){
                                //Open menu
                            }
                        }
						if(method_exists($entity, "onInteract")){
							$entity->onInteract($p, $item, $slot, $clickPos);
						}
					}
				}
				break;
                case($pk instanceof ContainerClosePacket):
                    if($pk->windowId === 0xff and isset($this->villagerId[$p->getName()])){
                        $pk = new RemoveEntityPacket();
                        $pk->entityUniqueId = $eid = $this->eventListener->villagerId[$p->getName()];
                        $p->dataPacket($pk);
                        unset($this->eventListener->villagerId[$p->getName()]);
                        unset($this->eventListener->recipes[$p->getName()]);
                    }
                    break;
                case ($pk instanceof EntityEventPacket):
                    if($pk->event === 62) { //TRADING_TRANSACTION
                        if (isset($this->villagerId[$p->getName()]) and $pk->entityRuntimeId === $this->eventListener->villagerId[$p->getName()] and isset($this->recipes[$p->getName()][$pk->data])) {
                            $recipe = $this->eventListener->recipes[$p->getName()][$pk->data];
                            //TODO: make trading inventory
                            $ev->setCancelled();
                        }
                    }
				break;
			/*case ($pk instanceof PlayerInputPacket):
				if(isset($p->riding) && $p->riding instanceof Minecart){
					$riding = $p->riding;
					$riding->setCurrentSpeed($pk->motionY);
				}
				// Cancel this event, this avoid the packet being unhandled
				$ev->setCancelled();
				break;*/
		}
	}

	/**
	 * @param DataPacketSendEvent $ev
	 *
	 * @priority LOWEST
	 */
	public function onPacketSend(DataPacketSendEvent $ev){
		$pk = $ev->getPacket();
		$p = $ev->getPlayer();
		switch(true){
			case ($pk instanceof StartGamePacket):
				if(Main::$registerDimensions){
					$pk->dimension = Utils::getDimension($p->getLevel());
				}
				break;
		}
	}
}
