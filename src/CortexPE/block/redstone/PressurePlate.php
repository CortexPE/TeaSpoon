<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace CortexPE\block\redstone;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\sound\GenericSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

class PressurePlate extends RedstoneSource {
	protected $activateTime = 0;
	protected $canActivate = true;

	/**
	 * PressurePlate constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return bool
	 */
	public function hasEntityCollision(){
		return true;
	}

	/**
	 * @param Entity $entity
	 */
	public function onEntityCollide(Entity $entity){
		if($this->getLevel()->getServer()->redstoneEnabled and $this->canActivate){
			if(!$this->isActivated()){
				$this->meta = 1;
				$this->getLevel()->setBlock($this, $this, true, false);
				$this->getLevel()->addSound(new GenericSound($this, 1000));
			}
			if(!$this->isActivated() or ($this->isActivated() and ($this->getLevel()->getServer()->getTick() % 30) == 0)){
				$this->activate();
			}
		}
	}

	/**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null){
		return ($this->meta == 0) ? false : true;
	}

	/**
	 * @param int $type
	 *
	 * @return bool|int
	 */
	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$below = $this->getSide(Vector3::SIDE_DOWN);
			if($below instanceof Transparent){
				$this->getLevel()->useBreakOn($this);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		/*if($type == Level::BLOCK_UPDATE_SCHEDULED){
			if($this->isActivated()){
				if(!$this->isCollided()){
					$this->meta = 0;
					$this->getLevel()->setBlock($this, $this, true, false);
					$this->deactivate();
					return Level::BLOCK_UPDATE_SCHEDULED;
				}
			}
		}*/

		return true;
	}

	public function checkActivation(){
		if($this->isActivated()){
			if((($this->getLevel()->getServer()->getTick() - $this->activateTime)) >= 3){
				$this->meta = 0;
				$this->getLevel()->setBlock($this, $this, true, false);
				$this->deactivate();
			}
		}
	}

	/*public function isCollided(){
		foreach($this->getLevel()->getEntities() as $p){
			$blocks = $p->getBlocksAround();
			if(isset($blocks[Level::blockHash($this->x, $this->y, $this->z)])) return true;
		}
		return false;
	}*/

	/**
	 * @param Item $item
	 * @param Block $block
	 * @param Block $target
	 * @param int $face
	 * @param float $fx
	 * @param float $fy
	 * @param float $fz
	 * @param Player|null $player
	 *
	 * @return bool|void
	 */
	public function place(Item $item, Block $block, Block $target, $face, Vector3 $clickVector, Player $player = null) : bool {
		$below = $this->getSide(Vector3::SIDE_DOWN);
		if($below instanceof Transparent) return;
		else $this->getLevel()->setBlock($block, $this, true, false);
	}

	/**
	 * @param Item $item
	 *
	 * @return mixed|void
	 */
	public function onBreak(Item $item, Player $player = null) : bool {
		if($this->isActivated()){
			$this->meta = 0;
			$this->deactivate();
		}
		$this->canActivate = false;
		$this->getLevel()->setBlock($this, new Air(), true);
	}

	/**
	 * @return float
	 */
	public function getHardness() : float {
		return 0.5;
	}

	/**
	 * @return float
	 */
	public function getResistance() : float {
		return 2.5;
	}
}
