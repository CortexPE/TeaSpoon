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

namespace CortexPE;

use CortexPE\entity\projectile\FishingHook;
use CortexPE\item\Elytra;
use pocketmine\entity\Vehicle;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\Player;
use pocketmine\Server as PMServer;

class Session {
	/** @var int */
	public $lastEnderPearlUse = 0,
		$lastChorusFruitEat = 0,
		$lastHeldSlot = 0;
	/** @var bool */
	public $usingElytra = false,
		$allowCheats = false,
		$fishing = false;
	/** @var null | FishingHook */
	public $fishingHook = null;
	/** @var array */
	public $clientData = [];
	/** @var Vehicle */
	public $vehicle = null;
	/** @var Player */
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function __destruct(){
		$this->unsetFishing();
	}

	public function unsetFishing(){
		$this->fishing = false;

		if($this->fishingHook instanceof FishingHook){
			$this->fishingHook->broadcastEntityEvent(EntityEventPacket::FISH_HOOK_TEASE, null, $this->fishingHook->getViewers());

			if(!$this->fishingHook->isClosed()){
				$this->fishingHook->flagForDespawn();
			}

			$this->fishingHook = null;
		}
	}

	public function getPlayer(): Player{
		return $this->player;
	}

	public function getServer(): PMServer{
		return $this->player->getServer();
	}

	public function damageElytra(int $damage = 1){
		if(!$this->player->isAlive() || !$this->player->isSurvival()){
			return;
		}
		$inv = $this->player->getArmorInventory();
		$elytra = $inv->getChestplate();
		if($elytra instanceof Elytra){
			$elytra->applyDamage($damage);
		}
	}

	public function isUsingElytra(): bool{
		if(!Main::$elytraEnabled){
			return false;
		}

		return ($this->player->getArmorInventory()->getChestplate() instanceof Elytra);
	}
}
