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

use CortexPE\Main;
use CortexPE\Session;
use CortexPE\Utils;
use pocketmine\event\{
	Listener, server\DataPacketReceiveEvent, server\DataPacketSendEvent
};
use pocketmine\network\mcpe\protocol\{
	PlayerActionPacket, StartGamePacket
};
use pocketmine\Player as PMPlayer;
use pocketmine\plugin\Plugin;

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
								$p->setGenericFlag(PMPlayer::DATA_FLAG_GLIDING, true); // Why isn't the datatype a byte?

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
					}
				}
				break;
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
