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

namespace CortexPE\item;

use CortexPE\entity\projectile\Arrow;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\Bow as PMBow;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class Bow extends PMBow {
	public function onReleaseUsing(Player $player): bool{
		if($player->isSurvival() and !$player->getInventory()->contains(Item::get(Item::ARROW, 0, 1))){
			$player->getInventory()->sendContents($player);

			return false;
		}
		$skipcheckItem = false;
		if(!$player->getInventory()->contains(Item::get(Item::ARROW, 0, 1))){
			$skipcheckItem = true;
		}

		if(!$skipcheckItem){
			$first = $player->getInventory()->getItem($player->getInventory()->first(Item::get(Item::ARROW, -1, 1), false));
		}else{
			$first = Item::get(Item::ARROW, 0, 1);
		}

		$nbt = Entity::createBaseNBT(
			$player->add(0, $player->getEyeHeight(), 0),
			$player->getDirectionVector(),
			($player->yaw > 180 ? 360 : 0) - $player->yaw,
			-$player->pitch
		);
		$nbt->setShort("Fire", $player->isOnFire() ? 45 * 60 : 0);
		if($first->getDamage() >= 1 && $first->getDamage() <= 36){
			$nbt->setShort("Potion", $first->getDamage() - 1);
		}

		$diff = $player->getItemUseDuration();
		$p = $diff / 20;
		$force = min((($p ** 2) + $p * 2) / 3, 1) * 2;


		/** @var Arrow $entity */
		$entity = new Arrow($player->getLevel(), $nbt, $player, $force == 2);
		//$entity = Entity::createEntity("Arrow", $player->getLevel(), $nbt, $player, $force == 2);
		if($entity instanceof Projectile){
			$ev = new EntityShootBowEvent($player, $this, $entity, $force);

			if($force < 0.1 or $diff < 5){
				$ev->setCancelled();
			}

			$player->getServer()->getPluginManager()->callEvent($ev);

			$entity = $ev->getProjectile(); //This might have been changed by plugins

			if($ev->isCancelled()){
				$entity->flagForDespawn();
				$player->getInventory()->sendContents($player);
			}else{
				$entity->setMotion($entity->getMotion()->multiply($ev->getForce()));
				$unbreaking = false;
				$infinity = false;
				if($this->hasEnchantments()){
					if($this->hasEnchantment(Enchantment::FLAME)){
						$enchantment = $this->getEnchantment(Enchantment::FLAME);
						$lvl = $enchantment->getLevel() + 4;
						$entity->setOnFire($lvl * 20);
					}
					if($this->hasEnchantment(Enchantment::UNBREAKING)){
						$enchantment = $this->getEnchantment(Enchantment::UNBREAKING);
						$lvl = $enchantment->getLevel() + 1;
						if(mt_rand(1, 100) >= intval(100 / $lvl)){
							$unbreaking = true;
						}
					}
					if($this->hasEnchantment(Enchantment::INFINITY)){
						$infinity = true;
					}
				}
				if($player->isSurvival()){
					if(!$infinity){
						$first->setCount(1);
						$player->getInventory()->removeItem($first);
					}
					if(!$unbreaking){
						$this->applyDamage(1);
					}
				}

				if($entity instanceof Projectile){
					$player->getServer()->getPluginManager()->callEvent($projectileEv = new ProjectileLaunchEvent($entity));
					if($projectileEv->isCancelled()){
						$ev->getProjectile()->flagForDespawn();
					}else{
						$ev->getProjectile()->spawnToAll();
						$player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_BOW);
					}
				}else{
					$entity->spawnToAll();
				}
			}
		}else{
			$entity->spawnToAll();
		}

		return true;
	}
}
