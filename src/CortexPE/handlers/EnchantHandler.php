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
use CortexPE\Main;
use CortexPE\Player;
use CortexPE\Utils;
use pocketmine\block\Block;
use pocketmine\entity\Attribute;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\Item;
use pocketmine\Player as PMPlayer;
use pocketmine\plugin\Plugin;

class EnchantHandler implements Listener {

	const BANE_OF_ARTHROPODS_AFFECTED_ENTITIES = [ // Based on https://minecraft.gamepedia.com/Enchanting#Bane_of_Arthropods ^_^
		"Spider", "Cave Spider",
		"Silverfish", "Endermite",
	];

	const WATER_IDS = [
		Block::STILL_WATER,
		Block::FLOWING_WATER,
	];

	/** @var Plugin */
	public $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * @param EntityDamageEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onDamage(EntityDamageEvent $ev){
		$e = $ev->getEntity();
		if($ev->isCancelled()){
			return;
		}
		if($ev instanceof EntityDamageByEntityEvent){ // TODO: ADD MORE ENCHANTS
			$d = $ev->getDamager();
			if($d instanceof PMPlayer && $e instanceof Living){
				$i = $d->getInventory()->getItemInHand();
				if($i->hasEnchantments()){
					foreach($i->getEnchantments() as $ench){
						if($ench->getLevel() <= 0) continue;
						switch($ench->getId()){
							case Enchantment::FIRE_ASPECT:
								$e->setOnFire(($ench->getLevel() * 4) * 20); // #BlamePMMP // Fire doesnt last for less than half a second. wtf.
								break;
							case Enchantment::KNOCKBACK:
								$ev->setKnockBack(($ev->getKnockBack() + 0.3) * $ench->getLevel());
								break;
							case Enchantment::PUNCH:
								if($d->getInventory()->getItemInHand()->getId() == Item::BOW){
									$ev->setKnockBack(($ev->getKnockBack() + 0.2) * $ench->getLevel());
								}
								break;
							case Enchantment::BANE_OF_ARTHROPODS:
								if(Utils::in_arrayi($e->getName(), self::BANE_OF_ARTHROPODS_AFFECTED_ENTITIES)){
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
								if($enchantment->getLevel() <= 0) continue;
								switch($enchantment->getId()){
									case Enchantment::THORNS:
										if($d instanceof PMPlayer){
											$armor = $d->getInventory()->getHelmet();
											if(mt_rand(1, 2) == 1 && $armor->getId() !== Block::AIR){
												$armorClone = clone $armor;
												if($armorClone->getDamage() - 3 > 0){
													$armorClone->setDamage($armorClone->getDamage() - 3);
												}else{
													$armorClone->setDamage(0);
												}
												$d->getInventory()->setHelmet($armorClone);
											}
											$armor = $d->getInventory()->getChestplate();
											if(mt_rand(1, 2) == 1 && $armor->getId() !== Block::AIR){
												$armorClone = clone $armor;
												if($armorClone->getDamage() - 3 > 0){
													$armorClone->setDamage($armorClone->getDamage() - 3);
												}else{
													$armorClone->setDamage(0);
												}
												$d->getInventory()->setChestplate($armorClone);
											}
											$armor = $d->getInventory()->getLeggings();
											if(mt_rand(1, 2) == 1 && $armor->getId() !== Block::AIR){
												$armorClone = clone $armor;
												if($armorClone->getDamage() - 3 > 0){
													$armorClone->setDamage($armorClone->getDamage() - 3);
												}else{
													$armorClone->setDamage(0);
												}
												$d->getInventory()->setLeggings($armorClone);
											}
											$armor = $d->getInventory()->getBoots();
											if(mt_rand(1, 2) == 1 && $armor->getId() !== Block::AIR){
												$armorClone = clone $armor;
												if($armorClone->getDamage() - 3 > 0){
													$armorClone->setDamage($armorClone->getDamage() - 3);
												}else{
													$armorClone->setDamage(0);
												}
												$d->getInventory()->setBoots($armorClone);
											}
											$d->attack(new EntityDamageEvent($e, EntityDamageEvent::CAUSE_CUSTOM, mt_rand($enchantment->getLevel(), 4 + $enchantment->getLevel())));
										}
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
		if(
			!(
				$ev->getCause() == EntityDamageEvent::CAUSE_STARVATION ||
				$ev->getCause() == EntityDamageEvent::CAUSE_MAGIC
			) &&
			$e instanceof Player
		){
			foreach($e->getInventory()->getArmorContents() as $armor){
				if($armor->hasEnchantments()){
					if($armor->hasEnchantment(Enchantment::PROTECTION) && ($ench = $armor->getEnchantment(Enchantment::PROTECTION))->getLevel() > 0){
						$ev->setDamage($ev->getDamage() - ((0.03 * $ench->getLevel()) * $ev->getDamage()));
						break;
					}
				}
			}
		}
		if(

			$ev->getCause() == EntityDamageEvent::CAUSE_PROJECTILE &&
			$e instanceof Player
		){
			foreach($e->getInventory()->getArmorContents() as $armor){
				if($armor->hasEnchantments()){
					if($armor->hasEnchantment(Enchantment::PROJECTILE_PROTECTION) && ($ench = $armor->getEnchantment(Enchantment::PROJECTILE_PROTECTION))->getLevel() > 0){
						$ev->setDamage($ev->getDamage() - ((0.04 * $ench->getLevel()) * $ev->getDamage()));
						break;
					}
				}
			}
		}
	}

	/**
	 * @param EntityShootBowEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onShoot(EntityShootBowEvent $ev){
		$p = $ev->getEntity();
		if($p instanceof PMPlayer && !$ev->isCancelled() && $ev->getBow()->hasEnchantments() && $ev->getBow()->hasEnchantment(Enchantment::INFINITY)){
			if($p->isSurvival()){
				$p->getInventory()->addItem(Item::get(Item::ARROW, 0, 1));
			}
		}
	}

	/**
	 * @param PlayerMoveEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onMove(PlayerMoveEvent $ev){
		if($ev->isCancelled()){
			return;
		}
		$p = $ev->getPlayer();
		$armor = $p->getInventory()->getBoots();
		if($armor->hasEnchantments() && $armor->hasEnchantment(Enchantment::DEPTH_STRIDER)){
			$lvl = $armor->getEnchantment(Enchantment::DEPTH_STRIDER)->getLevel();
			$att = $p->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
			if($lvl > 0){
				if(in_array($p->getLevel()->getBlock($p)->getId(), self::WATER_IDS)){
					$att->setValue($att->getDefaultValue() + ($att->getDefaultValue() * 0.75 * $lvl), true, true);

					Main::$TEMPAllowCheats[$ev->getPlayer()->getName()] = true;
				}else{
					if($att->getValue() == $att->getDefaultValue() + ($att->getDefaultValue() * 0.75 * $lvl)){
						$att->setValue($att->getDefaultValue(), true, true);

						Main::$TEMPAllowCheats[$ev->getPlayer()->getName()] = false;
					}
				}
			}else{
				if($att->getValue() == $att->getDefaultValue() + ($att->getDefaultValue() * 0.75 * $lvl)){
					$att->setValue($att->getDefaultValue(), true, true);

					Main::$TEMPAllowCheats[$ev->getPlayer()->getName()] = false;
				}
			}
		}

	}
}