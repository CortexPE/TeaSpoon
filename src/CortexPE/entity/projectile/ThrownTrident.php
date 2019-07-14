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

namespace CortexPE\entity\projectile;

use CortexPE\item\Trident;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\item\Item;
use pocketmine\math\RayTraceResult;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\TakeItemActorPacket;
use pocketmine\Player;
use pocketmine\Server;

class ThrownTrident extends Projectile {
	public const NETWORK_ID = self::TRIDENT;

	public $height = 0.35;
	public $width = 0.25;
	public $gravity = 0.10;

	protected $damage = 8;
	protected $age = 0;

	public function entityBaseTick(int $tickDiff = 1): bool{
		if($this->closed){
			return false;
		}

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->age > 1200){
			$this->flagForDespawn();
			$hasUpdate = true;
		}

		return $hasUpdate;
	}

	public function onCollideWithPlayer(Player $player): void{
		if($this->blockHit === \null){
			return;
		}

		$item = Item::nbtDeserialize($this->namedtag->getCompoundTag(Trident::TAG_TRIDENT));

		$playerInventory = $player->getInventory();

		if($player->isSurvival() and !$playerInventory->canAddItem($item)){
			return;
		}

		$pk = new TakeItemActorPacket();
		$pk->eid = $player->getId();
		$pk->target = $this->getId();
		$this->server->broadcastPacket($this->getViewers(), $pk);

		if(!$player->isCreative()){
			$playerInventory->addItem(clone $item);
		}
		$this->flagForDespawn();
	}

	public function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
		if($entityHit === $this->getOwningEntity()){
			return;
		}
		$this->applyGravity();
		parent::onHitEntity($entityHit, $hitResult);

		$pk = new PlaySoundPacket();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->soundName = "item.trident.hit";
		$pk->volume = 1;
		$pk->pitch = 1;
		Server::getInstance()->broadcastPacket($this->getViewers(), $pk);
	}

	public function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
		parent::onHitBlock($blockHit, $hitResult);
		$pk = new PlaySoundPacket();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->soundName = "item.trident.hit_ground";
		$pk->volume = 1;
		$pk->pitch = 1;
		Server::getInstance()->broadcastPacket($this->getViewers(), $pk);
	}
}
