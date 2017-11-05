<?php

namespace CortexPE\entity;

use pocketmine\item\Item as ItemItem;
use pocketmine\level\Level;
use pocketmine\nbt\tag\{ByteTag, CompoundTag};
use pocketmine\entity\Vehicle;

class Boat extends Vehicle{

    const NETWORK_ID = self::BOAT;

    public $height = 0.7;
    public $width = 1.6;
    public $gravity = 0.5;
    public $drag = 0.1;

    public function initEntity() {
        $this->setMaxHealth(4);
        parent::initEntity();
    }

    public function __construct(Level $level, CompoundTag $nbt) {
        if (!isset($nbt->WoodID)){
            $nbt->WoodID = new ByteTag("WoodID", 0);
        }
        parent::__construct($level, $nbt);
    }

    public function getWoodID() {
        return $this->namedtag["WoodID"];
    }

    public function getDrops(): array{
        return [
            ItemItem::get(ItemItem::BOAT, $this->getWoodID(), 1)
        ];
    }
}