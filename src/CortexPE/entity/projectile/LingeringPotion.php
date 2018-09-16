<?php

declare(strict_types = 1);

namespace CortexPE\entity\projectile;

use CortexPE\entity\object\AreaEffectCloud;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\item\Potion;
use pocketmine\level\{
	particle\ItemBreakParticle
};
use pocketmine\nbt\tag\{
	CompoundTag, DoubleTag, FloatTag, ListTag, ShortTag
};
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Server;

class LingeringPotion extends Throwable {

	public const NETWORK_ID = self::LINGERING_POTION;

	public const DATA_POTION_ID = 16;//TODO: update this
	public const TAG_POTION_ID = "PotionId";
	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;
	protected $gravity = 0.1;
	protected $drag = 0.05;

	public function initEntity(): void{
		if(!$this->namedtag->hasTag(self::TAG_POTION_ID, ShortTag::class)){
			$this->namedtag->setShort(self::TAG_POTION_ID, Potion::AWKWARD);
		}
		$this->getDataPropertyManager()->setShort(self::DATA_VARIANT, $this->getPotionId());
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_LINGER);

		parent::initEntity();
	}

	public function getPotionId(){
		return $this->namedtag->getShort(self::TAG_POTION_ID);
	}

	public function onHit(ProjectileHitEvent $event): void{
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
		$nbt->setInt(AreaEffectCloud::TAG_AGE, 0);
		$nbt->setShort(AreaEffectCloud::TAG_POTION_ID, $this->getPotionId());
		$nbt->setFloat(AreaEffectCloud::TAG_RADIUS, 3);
		$nbt->setFloat(AreaEffectCloud::TAG_RADIUS_ON_USE, -0.5);
		$nbt->setFloat(AreaEffectCloud::TAG_RADIUS_PER_TICK, -0.005);
		$nbt->setInt(AreaEffectCloud::TAG_WAIT_TIME, 10);
		$nbt->setInt(AreaEffectCloud::TAG_TILE_X, intval(round($this->getX())));
		$nbt->setInt(AreaEffectCloud::TAG_TILE_Y, intval(round($this->getY())));
		$nbt->setInt(AreaEffectCloud::TAG_TILE_Z, intval(round($this->getZ())));
		$nbt->setInt(AreaEffectCloud::TAG_DURATION, 600);
		$nbt->setInt(AreaEffectCloud::TAG_DURATION_ON_USE, 0);

		$aec = Entity::createEntity("AreaEffectCloud", $this->getLevel(), $nbt);
		if($aec instanceof Entity){
			$aec->spawnToAll();
		}

		$pk = new PlaySoundPacket();
		$pk->soundName = "random.glass";
		$pk->volume = 500;
		$pk->pitch = 1;
		Server::getInstance()->broadcastPacket($this->getViewers(), $pk);

		$this->flagForDespawn();
		parent::onHit($event);
	}
}
