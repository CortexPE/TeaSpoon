<?php

/*
 * Credits: https://github.com/thebigsmileXD/SimpleSpawner
*/

declare(strict_types = 1);

namespace CortexPE\tile;

use CortexPE\item\Record;
use CortexPE\utils\TextFormat;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\tile\Spawnable;

class Jukebox extends Spawnable {

	public const TAG_RECORD = "record";
	public const TAG_RECORD_ITEM = "recordItem";

	/** @var int */
	protected $record = 0; // default id...
	/** @var Item */
	protected $recordItem;

	private $loaded = false;

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

	public function dropMusicDisc(){
		$this->getLevel()->dropItem($this->add(0.5, 0.5, 0.5), $this->getRecordItem());
		$this->recordItem = Item::get(Item::AIR,0, 1);
	}

	public function setRecordItem(Record $disc){
		$this->recordItem = $disc;
		$this->record = $disc->getRecordId();
	}

	public function setRecordId(int $recordId){
		$this->record = $recordId;
	}

	public function getRecordItem() : Item{
		return ($this->recordItem instanceof Item ? $this->recordItem : Item::get(Item::AIR, 0, 1));
	}

	public function getRecordId() : int{
		return $this->record;
	}

	public function onUpdate() : bool {
		if($this->recordItem instanceof Record && !$this->loaded){
			$this->playMusicDisc();
			$this->loaded = true;
		}
		return true;
	}

	public function saveNBT() : void {
		parent::saveNBT();
		$this->namedtag->setTag($this->getRecordItem()->nbtSerialize(-1, self::TAG_RECORD_ITEM));
		$this->namedtag->setInt(self::TAG_RECORD, $this->getRecordId());
	}

	public function addAdditionalSpawnData(CompoundTag $nbt) : void {
		$nbt->setInt(self::TAG_RECORD, $this->getRecordId());

		$record = $this->getRecordItem() instanceof Item ? $this->getRecordItem() : Item::get(Item::AIR, 0, 1);
		$nbt->setTag($record->nbtSerialize(-1, self::TAG_RECORD_ITEM));
	}
}
