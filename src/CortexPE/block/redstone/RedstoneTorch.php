<?php
/**
 * Created by PhpStorm.
 * User: CortexPE
 * Date: 4/18/2018
 * Time: 11:04 PM
 */

namespace CortexPE\block\redstone;


use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RedstoneTorch extends RedstoneBase implements RedstonePowerSource {
	protected $id = self::REDSTONE_TORCH;

	protected $power = 15; // starting power

	public function __construct(int $meta = 0){
		parent::__construct(self::REDSTONE_TORCH, $meta);
	}

	public function getName(): string{
		return "Redstone Torch";
	}

	public function getBasePower(): int{
		return 15;
	}

	public function onNearbyBlockChange(): void{
		$below = $this->getSide(Vector3::SIDE_DOWN);
		$side = $this->getDamage();
		$faces = [
			0 => Vector3::SIDE_DOWN,
			1 => Vector3::SIDE_WEST,
			2 => Vector3::SIDE_EAST,
			3 => Vector3::SIDE_NORTH,
			4 => Vector3::SIDE_SOUTH,
			5 => Vector3::SIDE_DOWN,
		];

		if($this->getSide($faces[$side])->isTransparent() and !($side === Vector3::SIDE_DOWN and ($below->getId() === self::FENCE or $below->getId() === self::COBBLESTONE_WALL))){
			$this->getLevel()->useBreakOn($this);
		}
		unset($faces[$this->meta]);
		$this->powerSides($faces);
	}

	public function powerSides(array $sides): void{
		foreach($sides as $side){
			$block = $this->getSide($side);
			if($block instanceof RedstoneWire && $block->meta < $this->getPower()){ // todo
				$block->setActive($this->getBasePower(), $this);
			}
		}
	}

	public function getLightLevel(): int{
		return 7;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = \null): bool{
		$below = $this->getSide(Vector3::SIDE_DOWN);

		if(!$blockClicked->isTransparent() and $face !== Vector3::SIDE_DOWN){
			$faces = [
				Vector3::SIDE_UP    => 5,
				Vector3::SIDE_NORTH => 4,
				Vector3::SIDE_SOUTH => 3,
				Vector3::SIDE_WEST  => 2,
				Vector3::SIDE_EAST  => 1,
			];
			$this->meta = $faces[$face];
			$this->getLevel()->setBlock($blockReplace, $this, \true, \true);

			return \true;
		}elseif(!$below->isTransparent() or $below->getId() === self::FENCE or $below->getId() === self::COBBLESTONE_WALL){
			$this->meta = 0;
			$this->getLevel()->setBlock($blockReplace, $this, \true, \true);

			return \true;
		}

		return \false;
	}

	public function getVariantBitmask(): int{
		return 0;
	}
}