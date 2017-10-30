<?php

declare(strict_types = 1);

namespace CortexPE\block;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\Solid;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Obsidian extends Solid {

	protected $id = self::OBSIDIAN;

	/** @var Vector3 */
	private $temporalVector = null;

	public function __construct($meta = 0){
		$this->meta = $meta;
		if($this->temporalVector === null){
			$this->temporalVector = new Vector3(0, 0, 0);
		}
	}

	public function getName(): string{
		return "Obsidian";
	}

	public function getToolType(): int{
		return Tool::TYPE_PICKAXE;
	}

	public function getHardness(): float{
		return 35;
	}

	public function getDrops(Item $item): array{
		if($item->isPickaxe() >= Tool::TIER_DIAMOND){
			return [
				[Item::OBSIDIAN, 0, 1],
			];
		}else{
			return [];
		}
	}

	public function onBreak(Item $item, Player $player = null): bool{
		parent::onBreak($item);

		for($i = 0; $i <= 6; $i++){
			if($this->getSide($i)->getId() == self::PORTAL){
				break;
			}
			if($i == 6){
				return false;
			}
		}
		$block = $this->getSide($i);
		if($this->getLevel()->getBlock($this->temporalVector->setComponents($block->x - 1, $block->y, $block->z))->getId() == Block::PORTAL or
			$this->getLevel()->getBlock($this->temporalVector->setComponents($block->x + 1, $block->y, $block->z))->getId() == Block::PORTAL
		){//x方向
			for($x = $block->x; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $block->y, $block->z))->getId() == Block::PORTAL; $x++){
				for($y = $block->y; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y++){
					$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->z), new Air());
				}
				for($y = $block->y - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y--){
					$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->z), new Air());
				}
			}
			for($x = $block->x - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $block->y, $block->z))->getId() == Block::PORTAL; $x--){
				for($y = $block->y; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y++){
					$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->z), new Air());
				}
				for($y = $block->y - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y--){
					$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->z), new Air());
				}
			}
		}else{//z方向
			for($z = $block->z; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $block->y, $z))->getId() == Block::PORTAL; $z++){
				for($y = $block->y; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y++){
					$this->getLevel()->setBlock($this->temporalVector->setComponents($block->x, $y, $z), new Air());
				}
				for($y = $block->y - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y--){
					$this->getLevel()->setBlock($this->temporalVector->setComponents($block->x, $y, $z), new Air());
				}
			}
			for($z = $block->z - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $block->y, $z))->getId() == Block::PORTAL; $z--){
				for($y = $block->y; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y++){
					$this->getLevel()->setBlock($this->temporalVector->setComponents($block->x, $y, $z), new Air());
				}
				for($y = $block->y - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y--){
					$this->getLevel()->setBlock($this->temporalVector->setComponents($block->x, $y, $z), new Air());
				}
			}
		}
		return true;
	}
}