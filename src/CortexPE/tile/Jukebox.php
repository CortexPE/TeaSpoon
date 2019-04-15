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

namespace CortexPE\tile;

use CortexPE\item\Record;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\tile\Spawnable;

class Jukebox extends Spawnable {

	/** @var string */
	public const
		TAG_RECORD = "record",
		TAG_RECORD_ITEM = "recordItem";

	/** @var int */
	protected $record = 0; // default id...
	/** @var Item */
	protected $recordItem;
	/** @var bool */
	private $loaded = false;
	/** @var CompoundTag */
	private $nbt;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);

		if(!$nbt->hasTag(self::TAG_RECORD, IntTag::class)){
			$nbt->setInt(self::TAG_RECORD, 0);
		}
		$this->record = $nbt->getInt(self::TAG_RECORD);

		if(!$nbt->hasTag(self::TAG_RECORD_ITEM, CompoundTag::class)){
			$nbt->setTag((Item::get(Item::AIR, 0, 1))->nbtSerialize(-1, self::TAG_RECORD_ITEM));
		}
		$this->recordItem = Item::nbtDeserialize($nbt->getCompoundTag(self::TAG_RECORD_ITEM));
	}

	public function dropMusicDisc(){
		$this->getLevel()->dropItem($this->add(new Vector3(0.5, 0.5, 0.5)), new Item($this->getRecordItem()->getId()));
		$this->recordItem = Item::get(Item::AIR, 0, 1);
		$this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_STOP_RECORD);
	}

	public function getRecordItem(): Item{
		return ($this->recordItem instanceof Item ? $this->recordItem : Item::get(Item::AIR, 0, 1));
	}

	public function setRecordItem(Record $disc){
		$this->recordItem = $disc;
		$this->record = $disc->getRecordId();
	}

	public function setRecordId(int $recordId){
		$this->record = $recordId;
	}

	public function onUpdate(): bool{
		if($this->recordItem instanceof Record && !$this->loaded){
			$this->playMusicDisc();
			$this->loaded = true;
		}

		return true;
	}

	public function playMusicDisc(){
		$recordItem = $this->getRecordItem();
		if($recordItem instanceof Record){
			if($recordItem->getSoundId() > 0){
				$pk = new LevelSoundEventPacket();
				$pk->sound = $recordItem->getSoundId();
				$pk->position = $this->asVector3();
				$this->getLevel()->addChunkPacket($this->getX() >> 4, $this->getZ() >> 4, $pk);

				foreach($this->getLevel()->getEntities() as $entity){
					if($entity->distance($this) <= 65){
						if($entity instanceof Player){
							$entity->sendPopup(TextFormat::LIGHT_PURPLE . "Now Playing : C418 - " . $recordItem->getRecordName());
						}
					}
				}
			}
		}
	}

	public function saveNBT(): CompoundTag{
		$this->getNBT()->setTag($this->getRecordItem()->nbtSerialize(-1, self::TAG_RECORD_ITEM));
		$this->getNBT()->setInt(self::TAG_RECORD, $this->getRecordId());

		return parent::saveNBT();
	}

	public function getNBT(): CompoundTag{
		return $this->nbt;
	}

	public function getRecordId(): int{
		return $this->record;
	}

	public function addAdditionalSpawnData(CompoundTag $nbt): void{
		$nbt->setInt(self::TAG_RECORD, $this->getRecordId());

		$record = $this->getRecordItem() instanceof Item ? $this->getRecordItem() : Item::get(Item::AIR, 0, 1);
		$nbt->setTag($record->nbtSerialize(-1, self::TAG_RECORD_ITEM));
	}

	protected function readSaveData(CompoundTag $nbt): void{
		$this->nbt = $nbt;
	}

	protected function writeSaveData(CompoundTag $nbt): void{
		$nbt->setInt(self::TAG_RECORD, $this->getRecordId());

		$record = $this->getRecordItem() instanceof Item ? $this->getRecordItem() : Item::get(Item::AIR, 0, 1);
		$nbt->setTag($record->nbtSerialize(-1, self::TAG_RECORD_ITEM));
	}
}
