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

use CortexPE\item\enchantment\Enchantment;
use CortexPE\Player;
use CortexPE\Utils;
use pocketmine\block\Block;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\plugin\Plugin;
use pocketmine\Player as PMPlayer;

class EnchantHandler implements Listener {

	const BANE_OF_ARTHROPODS_AFFECTED_ENTITIES = [ // Based on https://minecraft.gamepedia.com/Enchanting#Bane_of_Arthropods ^_^
		"Spider", "Cave Spider",
		"Silverfish", "Endermite"
	];

	const WATER_IDS = [
		Block::STILL_WATER,
		Block::FLOWING_WATER
	];

	/** @var Plugin */
	public $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
	}

	public function onDamage(EntityDamageEvent $ev){
		$e = $ev->getEntity();
		if($ev instanceof EntityDamageByEntityEvent){ // TODO: ADD MORE ENCHANTS
			$d = $ev->getDamager();
			if($d instanceof PMPlayer && $e instanceof Living){
				$i = $d->getInventory()->getItemInHand();
				if($i->hasEnchantments()){
					foreach($i->getEnchantments() as $ench){
						if($ench->getLevel() <= 0)continue;
						switch($ench->getId()){
							case Enchantment::FIRE_ASPECT:
								$e->setOnFire(($ench->getLevel() * 4) * 20); // #BlamePMMP // Fire doesnt last for less than half a second. wtf.
								break;
							case Enchantment::KNOCKBACK:
								$ev->setKnockBack(($ev->getKnockBack() + 0.3) * $ench->getLevel());
								break;
							case Enchantment::BANE_OF_ARTHROPODS:
								if(Utils::in_arrayi($e->getName(),self::BANE_OF_ARTHROPODS_AFFECTED_ENTITIES)){
									$ev->setDamage($ev->getDamage() + ($ench->getLevel() * 2.5));
								}
								break;
							case Enchantment::POWER:
								if($d->getInventory()->getItemInHand()->getId() == Item::BOW){
									$ev->setDamage($ev->getDamage() + ((($ev->getDamage() * 0.25) * $ench->getLevel()) + 1));
								}
								break;
							case Enchantment::SMITE:
								$ev->setDamage($ev->getDamage() + ($ench->getLevel() * 2.5));
								break;
							case Enchantment::SHARPNESS:
								$ev->setDamage($ev->getDamage() + (($ench->getLevel() * 0.4) + 1));
								break;
						}
					}
				}
				if($e instanceof PMPlayer){
					foreach($e->getInventory()->getArmorContents() as $armorContent){
						if($armorContent->hasEnchantments()){
							foreach($armorContent->getEnchantments() as $enchantment){
								if($enchantment->getLevel() <= 0)continue;
								switch($enchantment->getId()){
									case Enchantment::THORNS:
										$d->attack(new EntityDamageEvent($e, EntityDamageEvent::CAUSE_ENTITY_ATTACK, mt_rand($enchantment->getLevel(), 3 + $enchantment->getLevel())));
										break;
								}
							}
						}
					}
				}
			}
		}
		if(
			(
				$ev->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION ||
				$ev->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION
			) &&
			$e instanceof Player
		){
			foreach($e->getInventory()->getArmorContents() as $armor){
				if($armor->hasEnchantments()){
					if($armor->hasEnchantment(Enchantment::BLAST_PROTECTION) && ($ench = $armor->getEnchantment(Enchantment::BLAST_PROTECTION))->getLevel() > 0){
						$ev->setDamage($ev->getDamage() - ((0.04 * $ench->getLevel()) * $ev->getDamage()));
						break;
					}
				}
			}
		}
		if(
			$ev->getCause() == EntityDamageEvent::CAUSE_FALL &&
			$e instanceof Player
		){
			foreach($e->getInventory()->getArmorContents() as $armor){
				if($armor->hasEnchantments()){
					if($armor->hasEnchantment(Enchantment::FEATHER_FALLING) && ($ench = $armor->getEnchantment(Enchantment::FEATHER_FALLING))->getLevel() > 0){
						$ev->setDamage($ev->getDamage() - ((0.06 * $ench->getLevel()) * $ev->getDamage()));
						break;
					}
				}
			}
		}
		if(
			(
				$ev->getCause() == EntityDamageEvent::CAUSE_FIRE ||
				$ev->getCause() == EntityDamageEvent::CAUSE_FIRE_TICK ||
				$ev->getCause() == EntityDamageEvent::CAUSE_LAVA
			) &&
			$e instanceof Player
		){
			foreach($e->getInventory()->getArmorContents() as $armor){
				if($armor->hasEnchantments()){
					if($armor->hasEnchantment(Enchantment::FIRE_PROTECTION) && ($ench = $armor->getEnchantment(Enchantment::FIRE_PROTECTION))->getLevel() > 0){
						$ev->setDamage($ev->getDamage() - ((0.02 * $ench->getLevel()) * $ev->getDamage()));
						break;
					}
				}
			}
		}
	}
}