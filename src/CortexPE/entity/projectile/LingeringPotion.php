<?php

declare(strict_types = 1);

namespace CortexPE\entity\projectile;

use CortexPE\item\Potion;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\{
	Level, particle\ItemBreakParticle
};
use pocketmine\nbt\tag\{
	CompoundTag, DoubleTag, FloatTag, IntTag, ListTag, ShortTag
};

class LingeringPotion extends Throwable {

	const NETWORK_ID = self::LINGERING_POTION;

	const DATA_POTION_ID = 16;//TODO: update this

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;
	protected $gravity = 0.1;
	protected $drag = 0.05;

	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
		if(!isset($nbt->PotionId)){
			$nbt->PotionId = new ShortTag("PotionId", Potion::AWKWARD);
		}
		parent::__construct($level, $nbt, $shootingEntity);
		unset($this->dataProperties[self::DATA_SHOOTER_ID]);
		$this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_SHORT, $this->getPotionId());
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_LINGER);
	}

	public function getPotionId(){
		return (int)$this->namedtag["PotionId"];
	}

	public function onUpdate(int $currentTick): bool{
		if($this->isCollided || $this->age > 1200){
			$this->getLevel()->addParticle(new ItemBreakParticle($this, ItemItem::get(ItemItem::LINGERING_POTION)));

			$aec = null;

			$nbt = new CompoundTag("", [
				new ListTag("Pos", [
					new DoubleTag("", $this->getX()),
					new DoubleTag("", $this->getY()),
					new DoubleTag("", $this->getZ()),
				]),
				new ListTag("Motion", [
					new DoubleTag("", 0),
					new DoubleTag("", 0),
					new DoubleTag("", 0),
				]),
				new ListTag("Rotation", [
					new FloatTag("", 0),
					new FloatTag("", 0),
				]),
			]);
			$nbt->Age = new IntTag("Age", 0);
			$nbt->PotionId = new ShortTag("PotionId", $this->getPotionId());
			$nbt->Radius = new FloatTag("Radius", 3);
			$nbt->RadiusOnUse = new FloatTag("RadiusOnUse", -0.5);
			$nbt->RadiusPerTick = new FloatTag("RadiusPerTick", -0.005);
			$nbt->WaitTime = new IntTag("WaitTime", 10);
			$nbt->TileX = new IntTag("TileX", (int)round($this->getX()));
			$nbt->TileY = new IntTag("TileY", (int)round($this->getY()));
			$nbt->TileZ = new IntTag("TileZ", (int)round($this->getZ()));
			$nbt->Duration = new IntTag("Duration", 600);
			$nbt->DurationOnUse = new IntTag("DurationOnUse", 0);

			$aec = Entity::createEntity("AreaEffectCloud", $this->getLevel(), $nbt);
			if($aec instanceof Entity){
				$aec->spawnToAll();
			}
			//$this->close();
			//$this->kill();
		}

		return parent::onUpdate($currentTick);
	}

	public function onCollideWithEntity(Entity $entity){
		return;
	}

}
