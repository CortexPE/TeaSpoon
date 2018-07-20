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

namespace CortexPE\handlers;

use CortexPE\item\enchantment\Enchantment;
use CortexPE\Utils;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\item\Axe;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\Pickaxe;
use pocketmine\item\TieredTool;
use pocketmine\Player as PMPlayer;
use pocketmine\plugin\Plugin;

class EnchantHandler implements Listener {
	/**
	 * TO-DO:
	 * [X] Protection (PMMP)
	 * [X] Fire Protection (PMMP)
	 * [X] Feather Falling (PMMP)
	 * [X] Blast protection (PMMP)
	 * [X] Projectile protection (PMMP)
	 * [X] Thorns
	 * [X] Respiration (PMMP)
	 * [X] Depth strider (Client Side)
	 * [X] Aqua affinity (Client Side)
	 * [X] Sharpness
	 * [X] Smite
	 * [X] Bane of athropods
	 * [X] Knockback
	 * [X] Fire aspect
	 * [X] Looting
	 * [X] Efficiency (PMMP)
	 * [X] Silk touch (PMMP)
	 * [X] Unbreaking (PMMP)
	 * [X] Fortune
	 * [X] Power
	 * [X] Punch
	 * [X] Flame
	 * [X] Infinity
	 * [X] Luck of the sea
	 * [X] Lure
	 * [ ] Frost walker (Very laggy as of now)
	 * [ ] Mending
	 */

	/** @var string */
	public const BANE_OF_ARTHROPODS_AFFECTED_ENTITIES = [ // Based on https://minecraft.gamepedia.com/Enchanting#Bane_of_Arthropods ^_^
		"Spider", "Cave Spider",
		"Silverfish", "Endermite",
	];

	/** @var int[] */
	public const WATER_IDS = [
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
		if($ev instanceof EntityDamageByEntityEvent){
			$d = $ev->getDamager();
			if(!($d instanceof Entity) || !$d->isAlive()){
				return;
			}
			if($d instanceof PMPlayer && $e instanceof Living){
				$i = $d->getInventory()->getItemInHand();
				$damage = $ev->getModifier(EntityDamageEvent::MODIFIER_ARMOR);
				$knockback = $ev->getKnockBack();
				foreach(Utils::getEnchantments($i) as $ench){
					$lvl = $ench->getLevel();
					switch($ench->getId()){
						case Enchantment::FIRE_ASPECT:
							$e->setOnFire(($lvl * 4) * 20);
							break;
						case Enchantment::KNOCKBACK:
							$ev->setKnockBack(($knockback + 0.3) * $lvl);
							break;
						case Enchantment::PUNCH:
							if($d->getInventory()->getItemInHand()->getId() == Item::BOW){
								$ev->setKnockBack(($knockback + 0.2) * $lvl);
							}
							break;
						case Enchantment::BANE_OF_ARTHROPODS:
							if(Utils::in_arrayi($e->getName(), self::BANE_OF_ARTHROPODS_AFFECTED_ENTITIES)){
								$ev->setModifier($damage + ($lvl * 2.5), EntityDamageEvent::MODIFIER_ARMOR);
							}
							break;
						case Enchantment::POWER:
							if($i->getId() == Item::BOW){
								$ev->setModifier($damage + ((($damage * 0.25) * $lvl) + 1), EntityDamageEvent::MODIFIER_ARMOR);
							}
							break;
						case Enchantment::SMITE:
							$ev->setModifier($damage + ($lvl * 2.5), EntityDamageEvent::MODIFIER_ARMOR);
							break;
						case Enchantment::SHARPNESS:
							$ev->setModifier($damage + (($lvl * 0.4) + 1), EntityDamageEvent::MODIFIER_ARMOR);
							break;
					}
				}
				if($e instanceof PMPlayer){
					foreach($e->getArmorInventory()->getContents() as $armorContent){
						if($armorContent->hasEnchantment(Enchantment::THORNS)){
							$enchantment = $armorContent->getEnchantment(Enchantment::THORNS);
							if($d instanceof PMPlayer){
								$armor = $d->getArmorInventory()->getHelmet();
								if(mt_rand(1, 2) == 1 && $armor->getId() !== Block::AIR){
									$armorClone = clone $armor;
									if($armorClone->getDamage() - 3 > 0){
										$armorClone->setDamage($armorClone->getDamage() - 3);
									}else{
										$armorClone->setDamage(0);
									}
									$d->getArmorInventory()->setHelmet($armorClone);
								}
								$armor = $d->getArmorInventory()->getChestplate();
								if(mt_rand(1, 2) == 1 && $armor->getId() !== Block::AIR){
									$armorClone = clone $armor;
									if($armorClone->getDamage() - 3 > 0){
										$armorClone->setDamage($armorClone->getDamage() - 3);
									}else{
										$armorClone->setDamage(0);
									}
									$d->getArmorInventory()->setChestplate($armorClone);
								}
								$armor = $d->getArmorInventory()->getLeggings();
								if(mt_rand(1, 2) == 1 && $armor->getId() !== Block::AIR){
									$armorClone = clone $armor;
									if($armorClone->getDamage() - 3 > 0){
										$armorClone->setDamage($armorClone->getDamage() - 3);
									}else{
										$armorClone->setDamage(0);
									}
									$d->getArmorInventory()->setLeggings($armorClone);
								}
								$armor = $d->getArmorInventory()->getBoots();
								if(mt_rand(1, 2) == 1 && $armor->getId() !== Block::AIR){
									$armorClone = clone $armor;
									if($armorClone->getDamage() - 3 > 0){
										$armorClone->setDamage($armorClone->getDamage() - 3);
									}else{
										$armorClone->setDamage(0);
									}
									$d->getArmorInventory()->setBoots($armorClone);
								}
								$d->attack(new EntityDamageEvent($e, EntityDamageEvent::CAUSE_CUSTOM, mt_rand($enchantment->getLevel(), 4 + $enchantment->getLevel())));
							}
							break; // do this only once...
						}
					}
				}
			}
		}
	}

	/**
	 * @param BlockBreakEvent $ev
	 *
	 * Attribution:
	 *  - Big thanks to @TheAz928 for the values... It really helped a lot! :D
	 *  - The onBreak function below is a refactored, bare-bones and more-human friendly version of his Fortune enchant handler...
	 *
	 * @priority HIGHEST
	 */
	public function onBreak(BlockBreakEvent $ev){
		if($ev->isCancelled()){
			return;
		}
		$block = $ev->getBlock();
		$item = $ev->getItem();
		$fortuneEnchantment = $item->getEnchantment(Enchantment::FORTUNE);
		if(!($fortuneEnchantment instanceof EnchantmentInstance)){ // all this method handles is the fortune enchant
			return;
		}
		$level = $fortuneEnchantment->getLevel() + 1;
		$rand = rand(1, $level);
		if($item instanceof TieredTool){
			switch($block->getId()){
				case Block::COAL_ORE:
					if($item instanceof Pickaxe){
						$ev->setDrops([Item::get(Item::COAL, 0, 1 + $rand)]);
					}
					break;
				case Block::LAPIS_ORE:
					if($item instanceof Pickaxe && $item->getTier() > TieredTool::TIER_WOODEN){
						$ev->setDrops([Item::get(Item::DYE, 4, rand(1, 4) + $rand)]);
					}
					break;
				case Block::GLOWING_REDSTONE_ORE:
				case Block::REDSTONE_ORE:
					if($item instanceof Pickaxe && $item->getTier() > TieredTool::TIER_WOODEN){
						$ev->setDrops([Item::get(Item::REDSTONE, 0, rand(2, 3) + $rand)]);
					}
					break;
				case Block::NETHER_QUARTZ_ORE:
					if($item instanceof Pickaxe && $item->getTier() > TieredTool::TIER_WOODEN){
						$ev->setDrops([Item::get(Item::QUARTZ, 0, rand(1, 2) + $rand)]);
					}
					break;
				case Block::DIAMOND_ORE:
					if($item instanceof Pickaxe && $item->getTier() == TieredTool::TIER_IRON){
						$ev->setDrops([Item::get(Item::DIAMOND, 0, 1 + $rand)]);
					}
					break;
				case Block::EMERALD_ORE:
					if($item instanceof Pickaxe && $item->getTier() == TieredTool::TIER_IRON){
						$ev->setDrops([Item::get(Item::EMERALD, 0, 1 + $rand)]);
					}
					break;
				case Block::POTATO_BLOCK:
					if($item instanceof Axe || $item instanceof Pickaxe){
						if($block->getDamage() >= 7){
							$ev->setDrops([Item::get(Item::POTATO, 0, rand(1, 3) + $rand)]);
						}
					}
					break;
				case Block::CARROT_BLOCK:
					if($item instanceof Axe || $item instanceof Pickaxe){
						if($block->getDamage() >= 7){
							$ev->setDrops([Item::get(Item::CARROT, 0, rand(1, 3) + $rand)]);
						}
					}
					break;
				case Block::BEETROOT_BLOCK:
					if($item instanceof Axe || $item instanceof Pickaxe){
						if($block->getDamage() >= 7){
							$ev->setDrops([Item::get(Item::BEETROOT, 0, rand(1, 3) + $rand)]);
						}
					}
					break;
				case Block::WHEAT_BLOCK:
					if($item instanceof Axe || $item instanceof Pickaxe){
						if($block->getDamage() >= 7){
							$ev->setDrops([Item::get(Item::SEEDS, 0, rand(1, 3) + $rand), Item::get(Item::WHEAT, 0, 1)]);
						}
					}
					break;
				case Block::MELON_BLOCK:
					if($item instanceof Axe || $item instanceof Pickaxe){
						$ev->setDrops([Item::get(Item::MELON, 0, rand(3, 9) + $rand)]);
					}
					break;
				case Block::LEAVES:
					if(rand(1, 100) <= 10 + $level * 2){
						$ev->setDrops([Item::get(Item::APPLE, 0, 1)]);
					}
					break;
			}
		}
	}

	/**
	 * @param EntityDeathEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onEntityDeath(EntityDeathEvent $ev){
		$ent = $ev->getEntity();
		if($ent instanceof Human){
			return;
		}
		$cause = $ent->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof PMPlayer){
				$item = $damager->getInventory()->getItemInHand();
				$enchantment = $item->getEnchantment(Enchantment::LOOTING);
				if($enchantment instanceof EnchantmentInstance){
					$drops = [];
					foreach($ev->getDrops() as $drop){
						$rand = rand(1, $enchantment->getLevel() + 1);
						$drop->setCount($drop->getCount() + $rand);
						$drops[] = $drop;
					}
					$ev->setDrops($drops);
				}
			}
		}
	}
}
