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
use CortexPE\item\ArmorDurability;
use CortexPE\item\Elytra;
use CortexPE\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\Player;

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
	/** @var Player */
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function __destruct(){
		$this->unsetFishing();
	}

	public function getPlayer(): Player{
		return $this->player;
	}

	public function useArmors(int $damage = 1){
		if(!$this->player->isAlive() || !$this->player->isSurvival()){
			return;
		}
		$inv = $this->player->getInventory();
		$size = $inv->getSize();
		$noDamage = false;
		for($i = $size; $i < $size + 4; $i++){
			$armor = $inv->getItem($i);

			if($armor->getId() == Item::ELYTRA){
				continue;
			}

			$dura = ArmorDurability::getDurability($armor->getId());
			if($dura == -1){
				continue;
			}

			if($armor->hasEnchantments()){
				foreach($armor->getEnchantments() as $ench){
					if($ench->getLevel() <= 0){
						continue;
					}
					switch($ench->getId()){
						case Enchantment::UNBREAKING:
							$rand = mt_rand(1, 100);
							$level = $ench->getLevel();
							switch($level){
								case 1:
									if($rand >= 80){
										$noDamage = true;
									}
									break;
								case 2:
									if($rand >= 73){
										$noDamage = true;
									}
									break;
								case 3:
									if($rand >= 70){
										$noDamage = true;
									}
									break;
								case 0:
									break;
							}
							break;
					}
				}
			}

			if(!$noDamage){
				$ac = clone $armor;
				$ac->setDamage($ac->getDamage() + $damage);
				if($ac->getDamage() >= $dura){
					$inv->setItem($i, Item::get(Item::AIR, 0, 1));
				}else{
					$inv->setItem($i, $ac);
				}

				$inv->sendArmorContents($inv->getViewers());
			}
		}
	}

	public function damageElytra(int $damage = 1){
		if(!$this->player->isAlive() || !$this->player->isSurvival()){
			return;
		}
		$inv = $this->player->getInventory();
		$elytra = $inv->getChestplate();
		$noDamage = false;
		if($elytra instanceof Elytra){
			$dura = ArmorDurability::getDurability(Item::ELYTRA);

			if($elytra->hasEnchantments()){
				foreach($elytra->getEnchantments() as $ench){
					if($ench->getLevel() <= 0){
						continue;
					}
					switch($ench->getId()){
						case Enchantment::UNBREAKING:
							$rand = mt_rand(1, 100);
							$level = $ench->getLevel();
							switch($level){
								case 1:
									if($rand >= 80){
										$noDamage = true;
									}
									break;
								case 2:
									if($rand >= 73){
										$noDamage = true;
									}
									break;
								case 3:
									if($rand >= 70){
										$noDamage = true;
									}
									break;
								case 0:
									break;
							}
							break;
					}
				}
			}

			if(!$noDamage){
				$ec = clone $elytra;
				$ec->setDamage($ec->getDamage() + $damage);
				if($ec->getDamage() >= $dura){
					$inv->setChestplate(Item::get(Item::AIR, 0, 1));
				}else{
					$inv->setChestplate($ec);
				}

				$inv->sendArmorContents($inv->getViewers());
			}
		}
	}

	public function isUsingElytra(): bool{
		if($this->player->getInventory()->getChestplate() instanceof Elytra){
			return true;
		}

		return false;
	}

	public function unsetFishing(){
		$this->fishing = false;

		if($this->fishingHook instanceof FishingHook){
			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->fishingHook->getId();
			$pk->event = EntityEventPacket::FISH_HOOK_TEASE;
			$this->player->getServer()->broadcastPacket($this->player->getLevel()->getPlayers(), $pk);

			$this->fishingHook->close();
			$this->fishingHook = null;
		}
	}
}