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

namespace CortexPE\inventory;


use CortexPE\tile\ShulkerBox;
use pocketmine\inventory\ContainerInventory;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class ShulkerBoxInventory extends ContainerInventory {

	/** @var ShulkerBox */
	protected $holder;

	public function __construct(ShulkerBox $tile){
		parent::__construct($tile);
	}

	public function getName(): string{
		return "Shulker Box";
	}

	public function getDefaultSize(): int{
		return 27;
	}

	public function getNetworkType(): int{
		return WindowTypes::CONTAINER;
	}

	public function onOpen(Player $who): void{
		parent::onOpen($who);
		if(count($this->getViewers()) === 1 && ($level = $this->getHolder()->getLevel()) instanceof Level){
			$this->broadcastBlockEventPacket($this->getHolder(), true);
			$level->broadcastLevelSoundEvent($this->getHolder()->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_SHULKERBOX_OPEN);
		}
	}

	public function getHolder(){
		return $this->holder;
	}

	protected function broadcastBlockEventPacket(Vector3 $vector, bool $isOpen){
		$pk = new BlockEventPacket();
		$pk->x = (int)$vector->x;
		$pk->y = (int)$vector->y;
		$pk->z = (int)$vector->z;
		$pk->eventType = 1;
		$pk->eventData = $isOpen ? 1 : 0;
		$this->getHolder()->getLevel()->addChunkPacket($this->getHolder()->getX() >> 4, $this->getHolder()->getZ() >> 4, $pk);
	}

	public function onClose(Player $who): void{
		if(count($this->getViewers()) === 1 && ($level = $this->getHolder()->getLevel()) instanceof Level){
			$this->broadcastBlockEventPacket($this->getHolder(), false);
			$level->broadcastLevelSoundEvent($this->getHolder()->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_SHULKERBOX_CLOSED);
		}
		parent::onClose($who);
	}
}