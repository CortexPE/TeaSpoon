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

namespace CortexPE\utils;

use CortexPE\block\EndPortal;
use CortexPE\block\Portal;
use CortexPE\item\Minecart;
use CortexPE\Utils;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\Server;

class EntityUtils extends Utils {
	/** @var Entity[] */ // These has to be set into the entity ebject... ¯\_(ツ)_/¯
	public static $ridingEntity = [];
	/** @var Entity[] */
	public static $riddenByEntity = [];

	public static function leashEntityToPlayer(Player $player, Entity $entity): bool{ // TODO: fix this
		$entityDPM = $entity->getDataPropertyManager();
		if($entityDPM->getByte(Entity::DATA_FLAG_LEASHED) != 1){
			$entityDPM->setByte(Entity::DATA_FLAG_LEASHED, 1, true);
			$entityDPM->setLong(Entity::DATA_LEAD_HOLDER_EID, $player->getId(), true);

			return true;
		}else{
			$entityDPM->removeProperty(Entity::DATA_FLAG_LEASHED);
			//$entityDPM->setByte(Entity::DATA_FLAG_LEASHED, 0, true);
			$entityDPM->setLong(Entity::DATA_LEAD_HOLDER_EID, -1, true);

			return false;
		}
	}

	public static function isInsideOfPortal(Entity $entity): bool{
		$block = $entity->getLevel()->getBlock($entity->floor());
		if($block instanceof Portal){
			return true;
		}

		return false;
	}

	public static function isInsideOfEndPortal(Entity $entity): bool{
		$block = $entity->getLevel()->getBlock($entity);
		if($block instanceof EndPortal){
			return true;
		}

		return false;
	}

	// Creds: Altay
	public static function mountEntity(Entity $vehicle, Entity $entity, int $type = EntityLink::TYPE_RIDER, bool $send = true): void{
		if(!isset(self::$ridingEntity[$entity->getId()]) and $entity !== $vehicle){
			self::$ridingEntity[$entity->getId()] = $vehicle;
			self::$riddenByEntity[$vehicle->getId()] = $entity;
			if($send){
				$dpm = $vehicle->getDataPropertyManager();
				$dpm->setVector3(Entity::DATA_RIDER_SEAT_POSITION, new Vector3(0, self::getMountedYOffset($vehicle), 0));

				if(!($vehicle instanceof Minecart)){
					$dpm->setByte(Entity::DATA_RIDER_ROTATION_LOCKED, 1);
					$dpm->setFloat(Entity::DATA_RIDER_MAX_ROTATION, 90);
					$dpm->setFloat(Entity::DATA_RIDER_MIN_ROTATION, -90);
				}

				$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_RIDING, true);
				$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_WASD_CONTROLLED, true);

				$pk = new SetEntityLinkPacket();
				$pk->link = new EntityLink($entity->getId(), $vehicle->getId(), $type);
				Server::getInstance()->broadcastPacket($entity->getViewers(), $pk);
				if($entity instanceof Player){
					$entity->dataPacket($pk);
				}
			}
		}
	}

	public static function dismountEntity(Entity $vehicle, Entity $entity, bool $send = true): void{
		if(isset(self::$ridingEntity[$entity->getId()])){
			unset(self::$ridingEntity[$entity->getId()]);
			unset(self::$riddenByEntity[$vehicle->getId()]);
			if($send){
				$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_RIDING, false);
				$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_WASD_CONTROLLED, false);

				$dpm = $vehicle->getDataPropertyManager();
				$dpm->removeProperty(Entity::DATA_RIDER_SEAT_POSITION);

				if(!($vehicle instanceof Minecart)){
					$dpm->removeProperty(Entity::DATA_RIDER_ROTATION_LOCKED);
					$dpm->removeProperty(Entity::DATA_RIDER_MAX_ROTATION);
					$dpm->removeProperty(Entity::DATA_RIDER_MIN_ROTATION);
				}

				$pk = new SetEntityLinkPacket();
				$pk->link = new EntityLink($entity->getId(), $vehicle->getId(), EntityLink::TYPE_REMOVE);
				Server::getInstance()->broadcastPacket($entity->getViewers(), $pk);
				if($entity instanceof Player){
					$entity->sendDataPacket($pk);
				}
			}
		}
	}

	private static function getMountedYOffset(Entity $entity): float{
		switch($entity->getId()){
			case Entity::BOAT:
				return 1.02001;
		}

		return 0;
	}
}