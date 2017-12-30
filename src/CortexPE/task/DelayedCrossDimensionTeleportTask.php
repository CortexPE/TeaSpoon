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
	Main, Utils
};
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\{
	ChangeDimensionPacket, PlayStatusPacket
};
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class DelayedCrossDimensionTeleportTask extends PluginTask {
	/** @var Player */
	protected $player;

	/** @var int */
	protected $dimension;

	/** @var Vector3 */
	protected $position;

	/** @var bool */
	protected $respawn;

	public function __construct(Plugin $owner, Player $player, int $dimension, Vector3 $position, bool $respawn = false){
		parent::__construct($owner);
		$this->player = $player;
		$this->dimension = $dimension;
		$this->position = $position;
		$this->respawn = $respawn;
	}

	public function onRun(int $currentTick){
		if(Utils::isDelayedTeleportCancellable($this->player, $this->dimension)){
			unset(Main::$onPortal[$this->player->getId()]);

			return false;
		}
		$pk = new ChangeDimensionPacket();
		$pk->dimension = $this->dimension;
		$pk->position = $this->position;
		$pk->respawn = $this->respawn;
		$this->player->dataPacket($pk);
		$this->player->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);
		$this->player->teleport($this->position);

		unset(Main::$onPortal[$this->player->getId()]);

		return true;
	}
}
