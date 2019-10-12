<?php


namespace CortexPE\tile;

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\tile\Spawnable;
use pocketmine\utils\Color;

class Sponge extends Spawnable
{
    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $this->applyBaseNBT($nbt);
    }
}