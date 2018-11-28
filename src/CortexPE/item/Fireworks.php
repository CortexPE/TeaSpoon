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

use CortexPE\entity\projectile\FireworkRocket;
use CortexPE\Main;
use CortexPE\Session;
use CortexPE\task\ElytraRocketBoostTrackingTask;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\utils\Random;

class Fireworks extends Item {

	public const TAG_FIREWORKS = "Fireworks";
	public const TAG_EXPLOSIONS = "Explosions";
	public const TAG_FLIGHT = "Flight";

	/** @var float */
	public $spread = 5.0;

	public function __construct($meta = 0){
		parent::__construct(Item::FIREWORKS, $meta, "Fireworks");
	}

	public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool{
		if(Main::$fireworksEnabled){
			if($this->getNamedTag()->hasTag(self::TAG_FIREWORKS, CompoundTag::class)){
				/*
				 * Credits to @thebigsmileXD (XenialDan)
				 * Original Repository: https://github.com/thebigsmileXD/fireworks
				 * Ported to TeaSpoon as TeaSpoon overrides the fireworks item (as Elytra Booster)
				 * Licensed under the MIT License (January 1, 2018)
				 * */
				$random = new Random();
				$yaw = $random->nextBoundedInt(360);
				$pitch = -1 * (float)(90 + ($random->nextFloat() * $this->spread - $this->spread / 2));
				$nbt = Entity::createBaseNBT($blockReplace->add(0.5, 0, 0.5), null, $yaw, $pitch);
				$tags = $this->getNamedTagEntry(self::TAG_FIREWORKS);
				if(!is_null($tags)){
					$nbt->setTag($tags);
				}
				$level = $player->getLevel();
				$rocket = new FireworkRocket($level, $nbt, $player, $this, $random);
				$level->addEntity($rocket);
				if($rocket instanceof Entity){
					if($player->isSurvival()){
						--$this->count;
					}
					$rocket->spawnToAll();

					return true;
				}
			}
		}

		return false;
	}

	public function onClickAir(Player $player, Vector3 $directionVector): bool{
		if(Main::$elytraEnabled && Main::$elytraBoostEnabled){
			$session = Main::getInstance()->getSessionById($player->getId());
			if($session instanceof Session){
				if($session->usingElytra && !$player->isOnGround()){
					if($player->getGamemode() != Player::CREATIVE && $player->getGamemode() != Player::SPECTATOR){
						$this->pop();
					}

					$damage = 0;
					$flight = 1;

					if(Main::$fireworksEnabled){
						if($this->getNamedTag()->hasTag(self::TAG_FIREWORKS, CompoundTag::class)){
							$fwNBT = $this->getNamedTag()->getCompoundTag(self::TAG_FIREWORKS);
							$flight = $fwNBT->getByte(self::TAG_FLIGHT);
							$explosions = $fwNBT->getListTag(self::TAG_EXPLOSIONS);
							if(count($explosions) > 0){
								$damage = 7;
							}
						}
					}

					$dir = $player->getDirectionVector();
					$player->setMotion($dir->multiply($flight * 1.25));
					$player->getLevel()->broadcastLevelSoundEvent($player->asVector3(), LevelSoundEventPacket::SOUND_LAUNCH);
					if(Main::$elytraBoostParticles){
						Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ElytraRocketBoostTrackingTask($player, 6), 4);
					}

					if($damage > 0){
						$ev = new EntityDamageEvent($player, EntityDamageEvent::CAUSE_CUSTOM, 7); // lets wait till PMMP Adds Fireworks damage constant
						$player->attack($ev);
					}
				}
			}
		}

		return true;
	}
}