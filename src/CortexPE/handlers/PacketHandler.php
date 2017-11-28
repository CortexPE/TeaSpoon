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

use CortexPE\item\ArmorDurability;
use CortexPE\item\Elytra;
use CortexPE\Main;
use pocketmine\event\{
	Listener, server\DataPacketReceiveEvent, server\DataPacketSendEvent
};
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\Player as PMPlayer;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class PacketHandler implements Listener {

	/** @var Plugin */
	public $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * @param DataPacketReceiveEvent $ev
	 *
	 * @priority LOWEST
	 */
	public function onPacketReceive(DataPacketReceiveEvent $ev){
		$pkr = $ev->getPacket();
		$p = $ev->getPlayer();
		if($pkr instanceof PlayerActionPacket){
			//Main::getInstance()->getLogger()->debug("Received PlayerActionPacket:" . $pkr->action . " from " . $p->getName());
			$session = Main::getInstance()->getSessionById($p->getId());
			switch($pkr->action){
				case PlayerActionPacket::ACTION_DIMENSION_CHANGE_ACK:
					// TODO: USE THIS FOR CROSS-DIMENSION TELEPORT
					break;

				case PlayerActionPacket::ACTION_DIMENSION_CHANGE_REQUEST:
					$p->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
					break;

				case PlayerActionPacket::ACTION_START_GLIDE:
					$p->setDataFlag(PMPlayer::DATA_FLAGS, PMPlayer::DATA_FLAG_GLIDING, true, PMPlayer::DATA_TYPE_BYTE);

					$session->usingElytra = true;
					$session->allowCheats = true;
					break;
				case PlayerActionPacket::ACTION_STOP_GLIDE:
					$p->setDataFlag(PMPlayer::DATA_FLAGS, PMPlayer::DATA_FLAG_GLIDING, false, PMPlayer::DATA_TYPE_BYTE);

					$session->usingElytra = false;
					$session->allowCheats = false;

					if($p->isSurvival() && $p->isAlive()){
						$inv = $p->getInventory();
						$elytra = $inv->getChestplate();
						if($elytra instanceof Elytra){
							$dura = ArmorDurability::OTHERS[$elytra->getId()];

							$cost = 1; // TODO: UNBREAKING AND STUFF
							$ec = clone $elytra;
							$ec->setDamage($ec->getDamage() + $cost);
							if($ec->getDamage() >= $dura){
								$inv->setChestplate(Item::get(Item::AIR, 0, 0));
							}else{
								$inv->setChestplate($ec);
							}

							$inv->sendArmorContents($inv->getViewers());
						}
					}
					break;
			}
		}
		if(Main::$debug){
			$name = (new \ReflectionClass(($packet = $ev->getPacket())))->getShortName();
			//$pinfo = $ev->getPlayer()->getName() ?? $ev->getPlayer()->getAddress() . ":" . $ev->getPlayer()->getPort();
			//$this->plugin->getLogger()->info("RECEIVE " . $name . " from " . $pinfo);
			$packet = $ev->getPacket();
			$packet->encode(); //other plugins might have changed the packet
			$header = "[Client -> Server 0x" . sprintf("%02d", dechex($packet->pid())) . "] " . $name . " (length " . strlen($packet->buffer) . ")";
			/*$binary = "";
			$ascii = preg_replace('#([^\x20-\x7E])#', ".", $packet->buffer);
			$binary .= $ascii . PHP_EOL;*/
			$binary = print_r($packet, true);
			file_put_contents($this->plugin->getDataFolder() . "packetlog.txt", $header . PHP_EOL . $binary . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND | LOCK_EX);
		}
	}

	public
	function onPacketSend(DataPacketSendEvent $ev){
		if(Main::$debug){ // Freezes
			if($ev->getPacket() instanceof BatchPacket){
				return;
			}
			$name = (new \ReflectionClass(($packet = $ev->getPacket())))->getShortName();
			//$pinfo = $ev->getPlayer()->getName() ?? $ev->getPlayer()->getAddress() . ":" . $ev->getPlayer()->getPort();
			//$this->plugin->getLogger()->info("SEND " . $name . " to " . $pinfo);

			$packet->encode(); //needed :(
			$header = "[Server -> Client 0x" . sprintf("%02d", dechex($packet->pid())) . "] " . $name . " (length " . strlen($packet->buffer) . ")";

			/*$binary = "";
				$ascii = preg_replace('#([^\x20-\x7E])#', ".", $packet->buffer);
				$binary .= $ascii . PHP_EOL;*/

			$binary = print_r($packet, true);
			file_put_contents($this->plugin->getDataFolder() . "packetlog.txt", $header . PHP_EOL . $binary . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND | LOCK_EX);
		}
	}
}