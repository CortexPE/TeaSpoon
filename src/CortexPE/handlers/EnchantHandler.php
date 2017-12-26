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
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\Player as PMPlayer;
use pocketmine\plugin\Plugin;

class EnchantHandler implements Listener {
	/**
	 * TO-DO:
	 * [X] Protection
	 * [X] Fire Protection
	 * [X] Feather Falling
	 * [X] Blast protection
	 * [X] Projectile protection
	 * [X] Thorns
	 * [X] Respiration (Client Side)
	 * [X] Depth strider (Client Side)
	 * [X] Aqua affinity (Client Side)
	 * [X] Sharpness
	 * [X] Smite
	 * [X] Bane of athropods
	 * [X] Knockback
	 * [X] Fire aspect
	 * [X] Looting
	 * [X] Efficiency (Client Side)
	 * [X] Silk touch
	 * [ ] Unbreaking
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
		if($ev instanceof EntityDamageByEntityEvent){
			$d = $ev->getDamager();
			if($d === null){
				return;
			}
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
		if($e instanceof PMPlayer){
			switch($ev->getCause()){
				case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
				case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
					foreach($e->getInventory()->getArmorContents() as $armor){
						if($armor->hasEnchantments()){
							if(($ench = $this->isEnchantedWith($armor, Enchantment::BLAST_PROTECTION))){
								if($ench === null){
									break 2;
								}
								$ev->setDamage($ev->getDamage() - ((0.04 * $ench->getLevel()) * $ev->getDamage()));
								break 2;
							}
						}
					}
					break;

				case EntityDamageEvent::CAUSE_FALL:
					foreach($e->getInventory()->getArmorContents() as $armor){
						if($armor->hasEnchantments()){
							if(($ench = $this->isEnchantedWith($armor, Enchantment::FEATHER_FALLING))){
								if($ench === null){
									break 2;
								}
								$ev->setDamage($ev->getDamage() - ((0.06 * $ench->getLevel()) * $ev->getDamage()));
								break 2;
							}
						}
					}
					break;

				case EntityDamageEvent::CAUSE_FIRE:
				case EntityDamageEvent::CAUSE_FIRE_TICK:
				case EntityDamageEvent::CAUSE_LAVA:
					foreach($e->getInventory()->getArmorContents() as $armor){
						if($armor->hasEnchantments()){
							if(($ench = $this->isEnchantedWith($armor, Enchantment::FIRE_PROTECTION))){
								if($ench === null){
									break 2;
								}
								$ev->setDamage($ev->getDamage() - ((0.02 * $ench->getLevel()) * $ev->getDamage()));
								break 2;
							}
						}
					}
					break;

				case EntityDamageEvent::CAUSE_STARVATION:
				case EntityDamageEvent::CAUSE_MAGIC:
					foreach($e->getInventory()->getArmorContents() as $armor){
						if($armor->hasEnchantments()){
							if(($ench = $this->isEnchantedWith($armor, Enchantment::PROTECTION))){
								if($ench === null){
									break 2;
								}
								$ev->setDamage($ev->getDamage() - ((0.03 * $ench->getLevel()) * $ev->getDamage()));
								break 2;
							}
						}
					}
					break;

				case EntityDamageEvent::CAUSE_PROJECTILE:
					foreach($e->getInventory()->getArmorContents() as $armor){
						if($armor->hasEnchantments()){
							if(($ench = $this->isEnchantedWith($armor, Enchantment::PROJECTILE_PROTECTION))){
								if($ench === null){
									break 2;
								}
								$ev->setDamage($ev->getDamage() - ((0.04 * $ench->getLevel()) * $ev->getDamage()));
								break 2;
							}
						}
					}
					break;
			}
		}
	}

	/**
	 * Checks if the Item is enchanted with a specific enchant ID
	 *
	 * @param Item $i
	 * @param int $enchantId
	 * @return \pocketmine\item\enchantment\EnchantmentInstance | null
	 */
	private function isEnchantedWith(Item $i, int $enchantId){
		if($i->getEnchantment($enchantId) !== null){
			if($i->getEnchantment($enchantId)->getLevel() > 0){
				return $i->getEnchantment($enchantId);
			}
		}

		return null;
	}

	/**
	 * @param EntityShootBowEvent $ev
	 *
	 * @priority HIGHEST
	 */
	public function onShoot(EntityShootBowEvent $ev){
		if($ev->isCancelled()){
			return;
		}
		$p = $ev->getEntity();
		if($ev->getBow()->hasEnchantments()){
			foreach($ev->getBow()->getEnchantments() as $enchantment){
				switch($enchantment->getId()){
					case Enchantment::INFINITY:
						if($p instanceof PMPlayer){
							if($p->isSurvival()){
								$p->getInventory()->addItem(Item::get(Item::ARROW, 0, 1));
							}
						}
						break;
					case Enchantment::FLAME:
						$level = $enchantment->getLevel();
						/** @var Arrow $arrow */
						$arrow = $ev->getProjectile();

						$modarrow = clone $arrow;
						$modarrow->namedtag->Fire = new ShortTag("Fire", $p->isOnFire() ? 80 * 10 * $level : 80 * $level);

						$ev->setProjectile($modarrow);
						$modarrow->setOnFire(80 * $level / 2);
						break;
				}
			}
		}
	}

	/**
	 * @param BlockBreakEvent $ev
	 *
	 * Attribution:
	 *  - Big thanks to @TheAz928 for the values... It really helped a lot! :D
	 *  - The onBreak function below is a refactored, bare-bones and more-human friendly version of his Fortune & SilkTouch enchant handler...
	 *
	 * @priority HIGHEST
	 */
	public function onBreak(BlockBreakEvent $ev){
		if($ev->isCancelled()){
			return;
		}
		$p = $ev->getPlayer();
		$block = $ev->getBlock();
		$item = $p->getInventory()->getItemInHand();
		foreach($item->getEnchantments() as $enchantment){
			switch($enchantment->getId()){
				case Enchantment::FORTUNE:
					$level = $item->getEnchantment(Enchantment::FORTUNE)->getLevel() + 1;
					$rand = rand(1, $item->getEnchantment(Enchantment::FORTUNE)->getLevel() + 1);
					switch($block->getId()){
						case Block::COAL_ORE:
							if($item->isPickaxe()){
								$ev->setDrops([Item::get(Item::COAL, 0, 1 + $rand)]);
							}
							break;
						case Block::LAPIS_ORE:
							if($item->isPickaxe() && $item->getId() !== Item::WOODEN_PICKAXE){
								$ev->setDrops([Item::get(Item::DYE, 4, rand(1, 4) + $rand)]);
							}
							break;
						case Block::GLOWING_REDSTONE_ORE:
						case Block::REDSTONE_ORE:
							if($item->isPickaxe() && $item->getId() !== Item::WOODEN_PICKAXE){
								$ev->setDrops([Item::get(Item::REDSTONE, 0, rand(2, 3) + $rand)]);
							}
							break;
						case Block::NETHER_QUARTZ_ORE:
							if($item->isPickaxe() && $item->getId() !== Item::WOODEN_PICKAXE){
								$ev->setDrops([Item::get(Item::QUARTZ, 0, rand(1, 2) + $rand)]);
							}
							break;
						case Block::DIAMOND_ORE:
							if($item->isPickaxe() && !in_array($item->getId(), [Item::WOODEN_PICKAXE, Item::STONE_PICKAXE, Item::GOLDEN_PICKAXE])){
								$ev->setDrops([Item::get(Item::DIAMOND, 0, 1 + $rand)]);
							}
							break;
						case Block::EMERALD_ORE:
							if($item->isPickaxe() && !in_array($item->getId(), [Item::WOODEN_PICKAXE, Item::STONE_PICKAXE, Item::GOLDEN_PICKAXE])){
								$ev->setDrops([Item::get(Item::EMERALD, 0, 1 + $rand)]);
							}
							break;
						case Block::POTATO_BLOCK:
							if($item->isAxe() or $item->isPickaxe()){
								if($block->getDamage() >= 7){
									$ev->setDrops([Item::get(Item::POTATO, 0, rand(1, 3) + $rand)]);
								}
							}
							break;
						case Block::CARROT_BLOCK:
							if($item->isAxe() or $item->isPickaxe()){
								if($block->getDamage() >= 7){
									$ev->setDrops([Item::get(Item::CARROT, 0, rand(1, 3) + $rand)]);
								}
							}
							break;
						case Block::BEETROOT_BLOCK:
							if($item->isAxe() or $item->isPickaxe()){
								if($block->getDamage() >= 7){
									$ev->setDrops([Item::get(Item::BEETROOT, 0, rand(1, 3) + $rand), Item::get(457, 0, 1)]);
								}
							}
							break;
						case Block::WHEAT_BLOCK:
							if($item->isAxe() or $item->isPickaxe()){
								if($block->getDamage() >= 7){
									$ev->setDrops([Item::get(Item::SEEDS, 0, rand(1, 3) + $rand), Item::get(Item::WHEAT, 0, 1)]);
								}
							}
							break;
						case Block::MELON_BLOCK:
							if($item->isAxe() or $item->isPickaxe()){
								$ev->setDrops([Item::get(Item::MELON, 0, rand(3, 9) + $rand)]);
							}
							break;
						case Block::LEAVES:
							if(rand(1, 100) <= 10 + $level * 2){
								$ev->setDrops([Item::get(Item::APPLE, 0, 1)]);
							}
							break;
					}
					break;
				case Enchantment::SILK_TOUCH:
					if($block->getId() !== Block::MOB_SPAWNER){
						$drops = [];
						foreach($ev->getDrops() as $drop){
							if($drop->getId() == $block->getId() and $drop->getDamage() == $block->getDamage()){
								$drops[] = $drop;
							}else{
								$it = Item::get($block->getId(), $block->getDamage(), 1);
								$drops[] = $it;
							}
						}
						$ev->setDrops($drops);
					}
					break;
			}
		}
	}

	public function onEntityDeath(EntityDeathEvent $ev){
		$ent = $ev->getEntity();
		if($ent instanceof PMPlayer){
			return;
		}
		$cause = $ent->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof PMPlayer){
				$item = $damager->getInventory()->getItemInHand();
				foreach($item->getEnchantments() as $enchantment){
					switch($enchantment->getId()){
						case Enchantment::LOOTING:
							$drops = [];
							foreach($ev->getDrops() as $drop){
								$rand = rand(1, $enchantment->getLevel() + 1);
								$drop->setCount($drop->getCount() + $rand);
								$drops[] = $drop;
							}
							$ev->setDrops($drops);
							break;
					}
				}
			}
		}
	}
}
