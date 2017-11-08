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

namespace CortexPE\handlers;

use CortexPE\Main;
use pocketmine\event\{Listener, server\DataPacketReceiveEvent};
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\plugin\Plugin;
use pocketmine\Player as PMPlayer;

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
	public function onDataPacket(DataPacketReceiveEvent $ev) {
		$pkr = $ev->getPacket();
		if($pkr instanceof PlayerActionPacket) {
			$p = $ev->getPlayer();
			switch($pkr->action){
				case PlayerActionPacket::ACTION_START_GLIDE:
					$p->setDataFlag(PMPlayer::DATA_FLAGS, PMPlayer::DATA_FLAG_GLIDING, true, PMPlayer::DATA_TYPE_BYTE);

					Main::$usingElytra[$p->getName()] = true;
					Main::$TEMPAllowCheats[$ev->getPlayer()->getName()] = true;
					break;
				case PlayerActionPacket::ACTION_STOP_GLIDE:
					$p->setDataFlag(PMPlayer::DATA_FLAGS, PMPlayer::DATA_FLAG_GLIDING, false, PMPlayer::DATA_TYPE_BYTE);

					Main::$usingElytra[$p->getName()] = false;
					Main::$TEMPAllowCheats[$ev->getPlayer()->getName()] = false;
					break;
			}
		}
	}
}