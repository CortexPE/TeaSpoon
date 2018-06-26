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
use CortexPE\Utils;
use pocketmine\{
	Player, Server
};
use pocketmine\block\{
	Air, Block, BlockToolType, Transparent
};
use pocketmine\entity\Entity;
use pocketmine\item\{
	Item
};
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\DimensionIds;

class Portal extends Transparent {

	/** @var int $id */
	protected $id = Block::PORTAL;

	public function __construct(int $id, int $meta = 0, ?string $name = \null, int $itemId = \null) {
        parent::__construct($id, $meta, $name, $itemId);
        $this->meta = $meta;
    }

    /**
	 * @return string
	 */
	public function getName(): string{
		return "Portal";
	}

	/**
	 * @return float
	 */
	public function getHardness(): float{
		return -1;
	}

	/**
	 * @return float
	 */
	public function getResistance(): float{
		return 0;
	}

	/**
	 * @return int
	 */
	public function getToolType(): int{
		return BlockToolType::TYPE_PICKAXE;
	}

	/**
	 * @return bool
	 */
	public function canPassThrough(): bool{
		return true;
	}

	/**
	 * @return bool
	 */
	public function hasEntityCollision(): bool{
		return true;
	}

	/**
	 * @param Item $item
	 * @param Player|null $player
	 * @return bool
	 */
	public function onBreak(Item $item, Player $player = null): bool{
		$block = $this;
		$temporalVector = new Vector3(0,0,0);
		if($this->getLevel()->getBlock($temporalVector->setComponents($block->x - 1, $block->y, $block->z))->getId() == Block::PORTAL or
			$this->getLevel()->getBlock($temporalVector->setComponents($block->x + 1, $block->y, $block->z))->getId() == Block::PORTAL
		){//x方向
			for($x = $block->x; $this->getLevel()->getBlock($temporalVector->setComponents($x, $block->y, $block->z))->getId() == Block::PORTAL; $x++){
				for($y = $block->y; $this->getLevel()->getBlock($temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y++){
					$this->getLevel()->setBlock($temporalVector->setComponents($x, $y, $block->z), new Air());
				}
				for($y = $block->y - 1; $this->getLevel()->getBlock($temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y--){
					$this->getLevel()->setBlock($temporalVector->setComponents($x, $y, $block->z), new Air());
				}
			}
			for($x = $block->x - 1; $this->getLevel()->getBlock($temporalVector->setComponents($x, $block->y, $block->z))->getId() == Block::PORTAL; $x--){
				for($y = $block->y; $this->getLevel()->getBlock($temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y++){
					$this->getLevel()->setBlock($temporalVector->setComponents($x, $y, $block->z), new Air());
				}
				for($y = $block->y - 1; $this->getLevel()->getBlock($temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y--){
					$this->getLevel()->setBlock($temporalVector->setComponents($x, $y, $block->z), new Air());
				}
			}
		}else{//z方向
			for($z = $block->z; $this->getLevel()->getBlock($temporalVector->setComponents($block->x, $block->y, $z))->getId() == Block::PORTAL; $z++){
				for($y = $block->y; $this->getLevel()->getBlock($temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y++){
					$this->getLevel()->setBlock($temporalVector->setComponents($block->x, $y, $z), new Air());
				}
				for($y = $block->y - 1; $this->getLevel()->getBlock($temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y--){
					$this->getLevel()->setBlock($temporalVector->setComponents($block->x, $y, $z), new Air());
				}
			}
			for($z = $block->z - 1; $this->getLevel()->getBlock($temporalVector->setComponents($block->x, $block->y, $z))->getId() == Block::PORTAL; $z--){
				for($y = $block->y; $this->getLevel()->getBlock($temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y++){
					$this->getLevel()->setBlock($temporalVector->setComponents($block->x, $y, $z), new Air());
				}
				for($y = $block->y - 1; $this->getLevel()->getBlock($temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y--){
					$this->getLevel()->setBlock($temporalVector->setComponents($block->x, $y, $z), new Air());
				}
			}
		}

		return true;
	}

	/**
	 * @param Item $item
	 * @param Block $block
	 * @param Block $target
	 * @param int $face
	 * @param Vector3 $facePos
	 * @param Player|null $player
	 * @return bool
	 */
	public function place(Item $item, Block $block, Block $target, int $face, Vector3 $facePos, Player $player = null): bool{
		if($player instanceof Player){
			$this->meta = $player->getDirection() & 0x01;
		}
		$this->getLevel()->setBlock($block, $this, true, true);

		return true;
	}

	/**
	 * @param Item $item
	 * @return array
	 */
	public function getDrops(Item $item): array{
		return [];
	}

	/**
	 * @param Entity $entity
	 */
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

							$posNether = Main::$netherLevel->getSafeSpawn();
							if(Main::$vanillaNetherTranfer){ //imperfect
								$x = (int)ceil($entity->getX() / 8);
								$y = (int)ceil($entity->getY() / 8);
								$z = (int)ceil($entity->getZ() / 8);

								if(!Main::$netherLevel->getBlockAt($x, $y - 1, $z)->isSolid() ||
									 Main::$netherLevel->getBlockAt($x, $y, $z)->isSolid() ||
									 Main::$netherLevel->getBlockAt($x, $y + 1, $z)->isSolid()
								){
									for($y2 = 125; $y2 >= 0; $y2--){ // 128 - 3
										if(Main::$netherLevel->getBlockAt($x, $y2 - 1, $z, true, false)->isSolid() &&
											!Main::$netherLevel->getBlockAt($x, $y2, $z, true, false)->isSolid() &&
											!Main::$netherLevel->getBlockAt($x, $y2 + 1, $z, true, false)->isSolid()
										){
											break; // this leaves us the y value of whatever integer it stopped...
										}
									}
									if($y2 <= 0){ // if the for loop stopped but didnt find a spot this should be zero...
										$y = mt_rand(10, 125);
									}else{
										$y = $y2;
									}
								}
								if(Utils::vector3XZDistance($posNether, $entity->asVector3()) <= 0.1){
									return;
								}
								$posNether->setComponents($x, $y, $z);
							}

							if($gm == Player::SURVIVAL || $gm == Player::ADVENTURE){
								Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask(Main::getInstance(), $entity, DimensionIds::NETHER, $posNether), 20 * 4);
							}else{
								Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask(Main::getInstance(), $entity, DimensionIds::NETHER, $posNether), 1);
							}
						}else{ // NETHER -> OVERWORLD
							$gm = $entity->getGamemode();

							$posOverworld = Main::$overworldLevel->getSafeSpawn();
							if(Main::$vanillaNetherTranfer){
								$x = (int)ceil($entity->getX() * 8);
								$y = (int)ceil($entity->getY() * 8);
								$z = (int)ceil($entity->getZ() * 8);

								if(!Main::$overworldLevel->getBlockAt($x, $y - 1, $z)->isSolid() ||
									Main::$overworldLevel->getBlockAt($x, $y, $z)->isSolid() ||
									Main::$overworldLevel->getBlockAt($x, $y + 1, $z)->isSolid()
								){
									for($y2 = 0; $y2 <= Level::Y_MAX; $y2++){
										if(Main::$overworldLevel->getBlockAt($x, $y2 - 1, $z, true, false)->isSolid() &&
											!Main::$overworldLevel->getBlockAt($x, $y2, $z, true, false)->isSolid() &&
											!Main::$overworldLevel->getBlockAt($x, $y2 + 1, $z, true, false)->isSolid()
										){
											break;
										}
									}
									if($y2 >= Level::Y_MAX){
										$y = mt_rand(10, Level::Y_MAX);
									}else{
										$y = $y2;
									}
								}
								if(Utils::vector3XZDistance($posOverworld, $entity->asVector3()) <= 0.1){
									return;
								}
								$posOverworld->setComponents($x, $y, $z);
							}

							if($gm == Player::SURVIVAL || $gm == Player::ADVENTURE){
								Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask(Main::getInstance(), $entity, DimensionIds::OVERWORLD, $posOverworld), 20 * 4);
							}else{
								Server::getInstance()->getScheduler()->scheduleDelayedTask(new DelayedCrossDimensionTeleportTask(Main::getInstance(), $entity, DimensionIds::OVERWORLD, $posOverworld), 1);
							}
						}
					}
				}
				// TODO: Add mob teleportation
			}
		}
	}
}