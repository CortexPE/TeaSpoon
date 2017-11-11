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

namespace CortexPE\task;

use pocketmine\network\mcpe\protocol\{
	ChangeDimensionPacket, PlayStatusPacket, types\DimensionIds
};
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server as PMServer;

class DelayedTeleportTask extends PluginTask {

	/** @var Plugin */
	protected $owner;

	/** @var Player */
	protected $player;

	public function __construct(Plugin $owner, Player $player){
		$this->owner = $owner;
		$this->player = $player;
	}

	public function onRun(int $currentTick){
		$pk = new ChangeDimensionPacket();
		$pk->dimension = DimensionIds::OVERWORLD;
		$pk->position = PMServer::getInstance()->getDefaultLevel()->getSafeSpawn();
		$pk->respawn = true;
		$this->player->dataPacket($pk);
		$this->player->teleport(PMServer::getInstance()->getDefaultLevel()->getSafeSpawn());
		$this->player->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
		if($this->player->isOnFire()){
			$this->player->setOnFire(0);
		}
	}
}