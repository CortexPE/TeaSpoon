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

use CortexPE\Main;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RedstoneTorch extends RedstoneSource {

	protected $id = self::REDSTONE_TORCH;
	protected $ignore = "";

	/**
	 * RedstoneTorch constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return int
	 */
	public function getLightLevel() : int {
		return 7;
	}

	/**
	 * @return int
	 */
	public function getLastUpdateTime(){
		return Main::getBlockTempData($this);
	}

	public function setLastUpdateTimeNow(){
		Main::setBlockTempData($this, $this->getLevel()->getServer()->getTick());
	}

	/**
	 * @return bool|int
	 */
	public function canCalcTurn(){
		if(!parent::canCalc()) return false;
		if($this->getLevel()->getServer()->getTick() != $this->getLastUpdateTime()) return true;

		return ($this->canScheduleUpdate() ? Level::BLOCK_UPDATE_SCHEDULED : false);
	}

	/**
	 * @return bool
	 */
	public function canScheduleUpdate(){
		return Main::$allowFrequencyPulse;
	}

	/**
	 * @return int
	 */
	public function getFrequency(){
		return Main::$pulseFrequency;
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return "Redstone Torch";
	}

	/**
	 * @param string $ignore
	 *
	 * @return bool
	 */
	public function turnOn($ignore = ""){
		$result = $this->canCalcTurn();
		$this->setLastUpdateTimeNow();
		if($result === true){
			$faces = [
				1 => 4,
				2 => 5,
				3 => 2,
				4 => 3,
				5 => 0,
				6 => 0,
				0 => 0,
			];
			$this->id = self::REDSTONE_TORCH;
			$this->getLevel()->setBlock($this, $this, true);
			$this->activateTorch([$faces[$this->meta]], [$ignore]);

			return true;
		}elseif($result === Level::BLOCK_UPDATE_SCHEDULED){
			$this->ignore = $ignore;
			$this->getLevel()->scheduleUpdate($this, 20 * $this->getFrequency());

			return true;
		}

		return false;
	}

	/**
	 * @param string $ignore
	 *
	 * @return bool
	 */
	public function turnOff($ignore = ""){
		$result = $this->canCalcTurn();
		$this->setLastUpdateTimeNow();
		if($result === true){
			$faces = [
				1 => 4,
				2 => 5,
				3 => 2,
				4 => 3,
				5 => 0,
				6 => 0,
				0 => 0,
			];
			$this->id = self::UNLIT_REDSTONE_TORCH;
			$this->getLevel()->setBlock($this, $this, true);
			$this->deactivateTorch([$faces[$this->meta]], [$ignore]);

			return true;
		}elseif($result === Level::BLOCK_UPDATE_SCHEDULED){
			$this->ignore = $ignore;
			$this->getLevel()->scheduleUpdate($this, 20 * $this->getFrequency());

			return true;
		}

		return false;
	}

	/**
	 * @param array $ignore
	 * @param array $notCheck
	 */
	public function activateTorch(array $ignore = [], $notCheck = []){
		if($this->canCalc()){
			$this->activated = true;
			/** @var Door $block */

			$sides = [Vector3::SIDE_EAST, Vector3::SIDE_WEST, Vector3::SIDE_SOUTH, Vector3::SIDE_NORTH, Vector3::SIDE_UP, Vector3::SIDE_DOWN];

			foreach($sides as $side){
				if(!in_array($side, $ignore)){
					$block = $this->getSide($side);
					if(!in_array($hash = Level::blockHash($block->x, $block->y, $block->z), $notCheck)){
						$this->activateBlock($block);
					}
				}
			}
			//$this->lastUpdateTime = $this->getLevel()->getServer()->getTick();
		}
	}

	/**
	 * @param array $ignore
	 *
	 * @return bool|void
	 */
	public function activate(array $ignore = []){
		$this->activateTorch($ignore);
	}

	/**
	 * @param array $ignore
	 *
	 * @return bool|void
	 */
	public function deactivate(array $ignore = []){
		$this->deactivateTorch($ignore);
	}

	/**
	 * @param array $ignore
	 * @param array $notCheck
	 */
	public function deactivateTorch(array $ignore = [], array $notCheck = []){
		if($this->canCalc()){
			$this->activated = false;
			/** @var Door $block */

			$sides = [Vector3::SIDE_EAST, Vector3::SIDE_WEST, Vector3::SIDE_SOUTH, Vector3::SIDE_NORTH];

			foreach($sides as $side){
				if(!in_array($side, $ignore)){
					$block = $this->getSide($side);
					if(!in_array($hash = Level::blockHash($block->x, $block->y, $block->z), $notCheck)){
						$this->deactivateBlock($block);
					}
				}
			}

			if(!in_array(Vector3::SIDE_DOWN, $ignore)){
				$block = $this->getSide(Vector3::SIDE_DOWN);
				if(!in_array($hash = Level::blockHash($block->x, $block->y, $block->z), $notCheck)){
					if(!$this->checkPower($block)){
						/** @var $block ActiveRedstoneLamp */
						if($block->getId() == Block::LIT_REDSTONE_LAMP) $block->turnOff();
					}

					$block = $this->getSide(Vector3::SIDE_DOWN, 2);
					$this->deactivateBlock($block);
				}
			}
			//$this->lastUpdateTime = $this->getLevel()->getServer()->getTick();
		}
	}

	/**
	 * @param int $type
	 *
	 * @return bool|int
	 */
	public function onUpdate($type){
		$faces = [
			1 => 4,
			2 => 5,
			3 => 2,
			4 => 3,
			5 => 0,
			6 => 0,
			0 => 0,
		];
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$below = $this->getSide(0);
			$side = $this->getDamage();

			if($this->getSide($faces[$side])->isTransparent() === true and
				!($side === 0 and ($below->getId() === self::FENCE or
						$below->getId() === self::COBBLESTONE_WALL
					))
			){
				$this->getLevel()->useBreakOn($this);

				return Level::BLOCK_UPDATE_NORMAL;
			}
			$this->activate([$faces[$side]]);
		}

		if($type == Level::BLOCK_UPDATE_SCHEDULED){
			if($this->id == self::UNLIT_REDSTONE_TORCH) $this->turnOn($this->ignore);
			else $this->turnOff($this->ignore);

			return Level::BLOCK_UPDATE_SCHEDULED;
		}

		return false;
	}

	public function onBreak(Item $item, Player $player = null) : bool {
		$this->getLevel()->setBlock($this, new Air(), true, false);
		$faces = [
			1 => 4,
			2 => 5,
			3 => 2,
			4 => 3,
			5 => 0,
			6 => 0,
			0 => 0,
		];
		$this->deactivate([$faces[$this->meta]]);
		Main::setBlockTempData($this);
		return true;
	}

	public function place(Item $item, Block $block, Block $target, $face, Vector3 $clickVector, Player $player = null) : bool {
		$below = $this->getSide(0);

		if($target->isTransparent() === false and $face !== 0){
			$faces = [
				1 => 5,
				2 => 4,
				3 => 3,
				4 => 2,
				5 => 1,
			];
			$this->meta = $faces[$face];
			$this->getLevel()->setBlock($block, $this, true, true);

			return true;
		}elseif(
			$below->isTransparent() === false or $below->getId() === self::FENCE or
			$below->getId() === self::COBBLESTONE_WALL or
			$below->getId() == Block::REDSTONE_LAMP or
			$below->getId() == Block::LIT_REDSTONE_LAMP
		){
			$this->meta = 0;
			$this->getLevel()->setBlock($block, $this, true, true);

			return true;
		}

		return false;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item): array{
		return [
			[Item::LIT_REDSTONE_TORCH, 0, 1],
		];
	}

	/**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null){
		return true;
	}
}
