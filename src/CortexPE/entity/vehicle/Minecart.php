<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author ClearSky
 * @link https://github.com/ClearSkyTeam/PocketMine-MP
 *
*/

namespace CortexPE\entity\vehicle;

use CortexPE\Main;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\Vehicle;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;

class Minecart extends Vehicle {

	const NETWORK_ID = self::MINECART;

	public $height = 0.7;
	public $width = 0.98;
	public $gravity = 0.4;
	public $drag = 0.1;

	/** @var Living */
	public $rider = null;

	/** @var Entity */
	public $linkedEntity = null;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
	}

	public function initEntity(){
		parent::initEntity();
		$this->setHealth(2);
		$this->setMaxHealth(2);
	}

	public function getDrops(): array{
		return [
			ItemItem::get(ItemItem::MINECART, 0, 1),
		];
	}

	public function onUpdate(int $currentTick): bool{
		$parent = parent::onUpdate($currentTick);
		if($this->rider !== null){
			$mot = $this->rider->getDirectionVector()->multiply(2);
			//$mot->y = -$this->gravity;
			$this->teleport($this->rider);
			$this->rider->setMotion($mot);
		}
		return $parent;
	}

	public function onInteract(Player $player, ItemItem $item, int $slot, Vector3 $clickPos): bool{
		$this->rider = $player;
		Main::getInstance()->getSessionById($player->getId())->vehicle = $this;

		/*$pk = new SetEntityLinkPacket();
		$link = new EntityLink($this->getId(), $player->getId(), 2, true); // todo: figure out what that last boolean is
		$pk->link = $link;
		$player->getServer()->broadcastPacket($this->getViewers(), $pk);

		$pk = new SetEntityLinkPacket();
		$link = new EntityLink($this->getId(), 0, 2, true); // todo: figure out what that last boolean is
		$pk->link = $link;
		$player->dataPacket($pk);
		$this->rider->getDataPropertyManager()->setVector3(Entity::DATA_RIDER_SEAT_POSITION, new Vector3(0, 0, 0));*/
		return true;
	}
}