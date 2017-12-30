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

declare(strict_types = 1);

namespace CortexPE\block;

use CortexPE\Main;
use CortexPE\task\DelayedCrossDimensionTeleportTask;
use pocketmine\block\{
	Air, Block, BlockToolType, Transparent
};
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\{
	Item
};
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\Player;
use pocketmine\Server;

class Portal extends Transparent {

	protected $id = Block::PORTAL;

	/** @var  Vector3 */
	private $temporalVector = null;

	public function __construct($meta = 0){
		$this->meta = $meta;
		if($this->temporalVector === null){
			$this->temporalVector = new Vector3(0, 0, 0);
		}
	}

	public function getName(): string{
		return "Portal";
	}

	public function getHardness(): float{
		return -1;
	}

	public function getResistance(): float{
		return 0;
	}

	public function getToolType(): int{
		return BlockToolType::TYPE_PICKAXE;
	}

	public function canPassThrough(): bool{
		return true;
	}

	public function hasEntityCollision(): bool{
		return true;
	}

	public function onBreak(Item $item, Player $player = null): bool{
		$block = $this;
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

	public function place(Item $item, Block $block, Block $target, int $face, Vector3 $facePos, Player $player = null): bool{
		if($player instanceof Player){
			$this->meta = $player->getDirection() & 0x01;
		}
		$this->getLevel()->setBlock($block, $this, true, true);

		return true;
	}

	public function getDrops(Item $item): array{
		return [];
	}

	public function onEntityCollide(Entity $entity): void{
		if(Main::$registerDimensions){
			if($entity->getLevel()->getSafeSpawn()->distance($entity->asVector3()) <= 0.1){
				return;
			}

			if(!isset(Main::$onPortal[$entity->getId()])){
				Main::$onPortal[$entity->getId()] = true;

				if($entity instanceof Player){
					if($entity->getLevel() instanceof Level){
						if($entity->getLevel()->getName() != Main::$netherName){ // OVERWORLD -> NETHER
							$gm = $entity->getGamemode();
							if($gm == Player::SURVIVAL || $gm == Player::ADVENTURE){
								Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask(Main::getInstance(), $entity, DimensionIds::NETHER, Main::$netherLevel->getSafeSpawn()), 20 * 4);
							} else {
								Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask(Main::getInstance(), $entity, DimensionIds::NETHER, Main::$netherLevel->getSafeSpawn()), 1);
							}
						} else { // NETHER -> OVERWORLD
							$gm = $entity->getGamemode();
							if($gm == Player::SURVIVAL || $gm == Player::ADVENTURE){
								Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask(Main::getInstance(), $entity, DimensionIds::OVERWORLD, Server::getInstance()->getDefaultLevel()->getSafeSpawn()), 20 * 4);
							} else {
								Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask(Main::getInstance(), $entity, DimensionIds::OVERWORLD, Server::getInstance()->getDefaultLevel()->getSafeSpawn()), 1);
							}
						}
					}
				}

				// TODO: Add mob teleportation
			}
		}
	}
}