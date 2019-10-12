<?php


namespace CortexPE\block;

use CortexPE\Main;
use CortexPE\tile\Sponge as SpongeTile;
use CortexPE\tile\Tile;
use CortexPE\utils\DyeUtils;
use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\Potion;
use pocketmine\item\TieredTool;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;
use pocketmine\utils\Color;

class Sponge extends Transparent
{
    protected $id = self::SPONGE_BLOCK;
    protected $itemId = Item::SPONGE;

    private $absorbRange = 7;
    private $absorbQuantity = 65;

    public function __construct($meta = 0){
        $this->meta = $meta;
    }

    public function getName(): string{
        return "Sponge";
    }

    public function getHardness(): float{
        return 2;
    }

    public function getToolType(): int{
        return BlockToolType::TYPE_NONE;
    }

    public function getToolHarvestLevel(): int{
        return TieredTool::TIER_WOODEN;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = \null): bool{
        Tile::createTile(Tile::SPONGE, $this->getLevel(), SpongeTile::createNBT($this, $face, $item, $player));

        $player->sendMessage("Placed a TeaSpoon sponge.");

        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function isFull(): bool{
        return $this->meta >= $this->absorbQuantity;
    }

    public function isEmpty(): bool{
        return $this->meta == 0;
    }
}