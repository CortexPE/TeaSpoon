<?php
/**
 * Created by PhpStorm.
 * User: CortexPE
 * Date: 4/18/2018
 * Time: 10:59 PM
 */

namespace CortexPE\block\redstone;

use CortexPE\Main;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RedstoneWire extends RedstoneBase implements RedstonePowerSource {

	protected $id = self::REDSTONE_WIRE;

	public function __construct(int $meta = 0){
		parent::__construct(self::REDSTONE_WIRE, $meta);
		$this->sourcePos = $this; // hackk
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = \null): bool{
		$parent = parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);

		return $parent;
	}

	public function getName(): string{
		return "Redstone Wire";
	}

	public function getBasePower(): int{
		return 0;
	}

	public function powerSides(array $sides): void{
		foreach($sides as $side){
			$block = $this->getSide($side);
			if($block->asVector3()->equals($this->getSource())) continue;
			if($block instanceof RedstoneWire){ // todo
				if($block->meta >= $this->getPower() && $this->level->getBlock($block->getSource()) instanceof RedstoneBase) continue;
				$val = ($this->meta - 1) + $block->meta;
				if($val < 0){
					$val = 0;
				}
				if($val > 15){
					$val = 15;
				}
				$block->setActive($val, $this);
				$block->updateMeta();
			}
		}
	}

	public function updateMeta(): void{
		$this->meta = $this->getPower();
		Main::getPluginLogger()->debug("updated meta... " . $this->getName() . " " . $this->asVector3() . " " . (string)$this->meta);
		$this->level->setBlock($this, $this, true, false); // don't update em so we don't run into a segfault? lol
		$this->powerSides([Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_WEST, Vector3::SIDE_SOUTH]);
	}

	public function checkPower(): int{
		$power = 0;
		foreach($this->getAllSides() as $side){
			if($side instanceof RedstoneBase){
				$power += $side->getPower();
			}
		}
		if($power > 15){
			$power = 15;
		}

		return $power;
	}
}