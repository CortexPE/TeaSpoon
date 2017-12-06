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
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\Player;

class Session {
	/** @var int */
	public $lastEnderPearlUse = 0,
		$lastChorusFruitEat = 0;
	/** @var bool */
	public $skipCheck = false,
		$usingElytra = false,
		$allowCheats = false,
		$fishing = false;
	/** @var null | FishingHook */
	public $fishingHook = null;
	/** @var Player */
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function __destruct(){
		if($this->fishingHook !== null){
			$this->fishingHook->flagForDespawn();
		}
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
		for($i = $size; $i < $size + 4; $i++){
			$armor = $inv->getItem($i);

			if($armor->getId() == Item::ELYTRA){
				continue;
			}

			$dura = ArmorDurability::getDurability($armor->getId());
			if($dura == -1){
				continue;
			}

			$unbreakingEnchant = $armor->getEnchantment(Enchantment::UNBREAKING);
			if($armor->hasEnchantment(Enchantment::UNBREAKING) && $unbreakingEnchant->getLevel() > 0){
				$rand = mt_rand(1, 100);
				$level = $unbreakingEnchant->getLevel();
				switch($level){
					case 1:
						if($rand >= 80){
							return;
						}
						break;
					case 2:
						if($rand >= 73){
							return;
						}
						break;
					case 3:
						if($rand >= 70){
							return;
						}
						break;
					case 0:
						break;
				}
			}

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

	public function damageElytra(int $damage = 1){
		if(!$this->player->isAlive() || !$this->player->isSurvival()){
			return;
		}
		$inv = $this->player->getInventory();
		$elytra = $inv->getChestplate();
		if($elytra instanceof Elytra){
			$dura = ArmorDurability::getDurability(Item::ELYTRA);

			$unbreakingEnchant = $elytra->getEnchantment(Enchantment::UNBREAKING);
			if($elytra->hasEnchantment(Enchantment::UNBREAKING) && $unbreakingEnchant->getLevel() > 0){
				$rand = mt_rand(1, 100);
				$level = $unbreakingEnchant->getLevel();
				switch($level){
					case 1:
						if($rand >= 80){
							return;
						}
						break;
					case 2:
						if($rand >= 73){
							return;
						}
						break;
					case 3:
						if($rand >= 70){
							return;
						}
						break;
					case 0:
						break;
				}
			}

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


	public function isUsingElytra(): bool{
		if($this->player->getInventory()->getChestplate() instanceof Elytra){
			return true;
		}

		return false;
	}
}