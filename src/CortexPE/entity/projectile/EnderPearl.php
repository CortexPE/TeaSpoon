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

namespace CortexPE\entity\projectile;

use CortexPE\Main;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;

class EnderPearl extends Throwable {
	const NETWORK_ID = self::ENDER_PEARL;

	public function onUpdate(int $currentTick): bool{
		$p = $this->getOwningEntity();
		if($this->isCollided || $this->age > 1200){
			if($this->y > 0 && $p instanceof Player){
				$pk1 = new LevelEventPacket();
				$pk1->data = 0;
				$pk1->evid = 2010; // Portal particles n stuff.
				$pk1->position = new Vector3($p->getX(), $p->getY(), $p->getZ());
				$p->getServer()->broadcastPacket($p->getLevel()->getPlayers(), $pk1);

				$p->teleport($this->getPosition());
				$p->attack(new EntityDamageEvent($p, EntityDamageEvent::CAUSE_FALL, Main::$ePearlDamage));

				$pk2 = new LevelEventPacket();
				$pk2->data = 0;
				$pk2->evid = 2010;
				$pk2->position = new Vector3($p->getX(), $p->getY(), $p->getZ());
				$p->getServer()->broadcastPacket($p->getLevel()->getPlayers(), $pk2);

				$pk3 = new LevelEventPacket();
				$pk3->data = 0;
				$pk3->evid = LevelEventPacket::EVENT_SOUND_ENDERMAN_TELEPORT;
				$pk3->position = new Vector3($p->getX(), $p->getY(), $p->getZ());
				$p->getServer()->broadcastPacket($p->getLevel()->getPlayers(), $pk3);
			}

			$this->close();
		}

		return parent::onUpdate($currentTick);
	}
}