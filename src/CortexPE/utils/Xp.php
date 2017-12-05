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

use CortexPE\entity\Lightning;
use CortexPE\entity\XPOrb;
use CortexPE\Utils;
use pocketmine\block\Block;
use pocketmine\entity\Animal;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Monster;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;

class Xp extends Utils {
	// [ROT13 Encoded and is pretty Explicit] Jul gur shpx unfa'g CZZC Vzcyrzragrq n CEBCRE KC Flfgrz Lrg? Guvf vf Shpxvat fghcvq naq vf bar bs gur znal ernfbaf jul Crbcyr ybir fcbbaf -_-
	// hacky code to *SOMEHOW* get it working...

	// TODO: Base it all from this list: https://minecraft.gamepedia.com/Experience#Experience_amounts_by_source

	public static function addXp(Player $player, int $amount){
		$add = self::getLevelFromXp($player->getTotalXp() + $amount);
		$player->setXpProgress($add[1]);
		$player->setXpLevel(intval($player->getXpLevel() + round($player->getXpProgress())));
		self::saveData($player);
	}

	private static function saveData(Player $player){
		if(!isset($player->namedtag->XpLevel) or !($player->namedtag->XpLevel instanceof IntTag)){
			$player->namedtag->XpLevel = new IntTag("XpLevel", $player->getXpLevel());
		}else{
			$player->namedtag["XpLevel"] = $player->getXpLevel();
		}

		if(!isset($player->namedtag->XpP) or !($player->namedtag->XpP instanceof FloatTag)){
			$player->namedtag->XpP = new FloatTag("XpP", $player->getXpProgress());
		}else{
			$player->namedtag["XpP"] = $player->getXpProgress();
		}

		if(!isset($player->namedtag->XpTotal) or !($player->namedtag->XpTotal instanceof IntTag)){
			$player->namedtag->XpTotal = new IntTag("XpTotal", $player->getTotalXp());
		}else{
			$player->namedtag["XpTotal"] = $player->getTotalXp();
		}
		$player->getServer()->saveOfflinePlayerData($player->getName(), $player->namedtag, true);
	}

	public static function takeXp(Player $player, int $amount){
		if(($player->getTotalXp() - $amount) >= 0){
			$add = self::getLevelFromXp($player->getTotalXp() - $amount);
			$player->setXpProgress($add[1]);
			$player->setXpLevel(intval($player->getXpLevel() + round($player->getXpProgress())));
			self::saveData($player);
		}
	}

	public static function getXpDropsForEntity(Entity $e): int{
		switch($e::NETWORK_ID){
			case Lightning::NETWORK_ID:
				return 0;
			default:
				if($e instanceof Monster){
					switch($e->getName()){
						default:
							return 5;
					}
				}elseif($e instanceof Animal){
					switch($e->getName()){
						default:
							return mt_rand(1, 3);
					}
				}elseif($e instanceof Human){
					return 7;
				}

				return 0;
		}
	}

	public static function getXpDropsForBlock(Block $b): int{
		switch($b->getId()){
			case Block::COAL_ORE:
				return mt_rand(0, 2);

			case Block::DIAMOND_ORE:
			case Block::EMERALD_ORE:
				return mt_rand(3, 7);

			case Block::LAPIS_ORE:
			case Block::NETHER_QUARTZ_ORE:
				return mt_rand(2, 5);

			case Block::REDSTONE_ORE:
				return mt_rand(1, 5);

			case Block::MONSTER_SPAWNER:
				return mt_rand(15, 43);

			default:
				return 0;
		}
	}

	public static function spawnXpOrb(Vector3 $pos, Level $lvl, int $exp): XPOrb{
		$nbt = XPOrb::createBaseNBT($pos);
		$nbt->setLong("Experience", $exp);
		/** @var XPOrb $entity */
		$entity = Entity::createEntity("XPOrb", $lvl, $nbt);
		$entity->spawnToAll();

		return $entity;
	}
}