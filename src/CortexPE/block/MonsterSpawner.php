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

namespace CortexPE\block;

use CortexPE\Main;
use CortexPE\tile\MobSpawner;
use pocketmine\block\{
	Block, MonsterSpawner as SpawnerPM
};
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

class MonsterSpawner extends SpawnerPM {

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated(): bool{
		return true;
	}

	/**
	 * @param Item $item
	 * @param Player|null $player
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null): bool{
		if(Main::$mobSpawnerEnable && $item->getId() == Item::SPAWN_EGG){
			$tile = $this->getLevel()->getTile($this);
			if(!($tile instanceof MobSpawner)){
				$nbt = MobSpawner::createNBT($this);
				$tile = Tile::createTile(Tile::MOB_SPAWNER, $this->getLevel(), $nbt);
				if($tile instanceof MobSpawner){
					$tile->setEntityId($item->getDamage());
					if(!$player->isCreative()){
						$item->pop();
					}
					return true;
				}
			}
		}
		return false;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = \null): bool{
		parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
		$eID = null;
		$nbt = MobSpawner::createNBT($this, $face, $item, $player);
		if($item->getNamedTag()->getTag(MobSpawner::TAG_ENTITY_ID) !== null){
			foreach(
				[
					MobSpawner::TAG_ENTITY_ID,
					MobSpawner::TAG_DELAY,
					MobSpawner::TAG_MIN_SPAWN_DELAY,
					MobSpawner::TAG_MAX_SPAWN_DELAY,
					MobSpawner::TAG_SPAWN_COUNT,
					MobSpawner::TAG_SPAWN_RANGE,
				] as $tag_name){
				$tag = $item->getNamedTag()->getTag($tag_name);
				if($tag !== null){
					$nbt->setTag($tag);
				}
			}
		} elseif(($meta = $item->getDamage()) != 0){
			$nbt->setInt(MobSpawner::TAG_ENTITY_ID, $meta);
		} else {
			return true;
		}
		Tile::createTile(Tile::MOB_SPAWNER, $this->getLevel(), $nbt);

		return true;
	}

	public function getSilkTouchDrops(Item $item): array{
		$tile = $this->getLevel()->getTile($this);
		if($tile instanceof MobSpawner){
			return [
				ItemFactory::get(Item::MONSTER_SPAWNER, 0, 1, $tile->getCleanedNBT()),
			];
		}

		return parent::getSilkTouchDrops($item);
	}

	public function isAffectedBySilkTouch(): bool{
		return Main::$silkSpawners;
	}
}
