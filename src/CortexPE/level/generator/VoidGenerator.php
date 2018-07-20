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
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

declare(strict_types = 1);

namespace CortexPE\level\generator;

use pocketmine\block\Block;
use pocketmine\level\{
	ChunkManager, format\Chunk, generator\Generator
};
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class VoidGenerator extends Generator {
	/** @var ChunkManager */
	protected $level;
	/** @var Chunk */
	private $chunk;
	/** @var Random */
	protected $random;
	private $options;
	/** @var Chunk */
	private $emptyChunk = null;

	/**
	 * Void constructor.
	 *
	 * @param array $settings
	 */
	public function __construct(array $settings = []){
		$this->options = $settings;
	}

	/**
	 * @return array
	 */
	public function getSettings(): array{
		return [];
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return "Void";
	}

	/**
	 * @param ChunkManager $level
	 * @param Random $random
	 *
	 * @return void
	 */
	public function init(ChunkManager $level, Random $random) : void{
		$this->level = $level;
		$this->random = $random;
	}

	/**
	 * @param $chunkX
	 * @param $chunkZ
	 *
	 * @return void
	 */
	public function generateChunk(int $chunkX, int $chunkZ) : void{
		if($this->emptyChunk === null){
			$this->chunk = clone $this->level->getChunk($chunkX, $chunkZ);
			$this->chunk->setGenerated();

			for($Z = 0; $Z < 16; ++$Z){
				for($X = 0; $X < 16; ++$X){
					$this->chunk->setBiomeId($X, $Z, 1);
					for($y = 0; $y < 128; ++$y){
						$this->chunk->setBlockId($X, $y, $Z, Block::AIR);
					}
				}
			}

			$spawn = $this->getSpawn();
			if($spawn->getX() >> 4 === $chunkX and $spawn->getZ() >> 4 === $chunkZ){
				$this->chunk->setBlockId(0, 64, 0, Block::GRASS);
			}else{
				$this->emptyChunk = clone $this->chunk;
			}
		}else{
			$this->chunk = clone $this->emptyChunk;
		}

		$chunk = clone $this->chunk;
		$chunk->setX($chunkX);
		$chunk->setZ($chunkZ);
		$this->level->setChunk($chunkX, $chunkZ, $chunk);
	}

	/**
	 * @return Vector3
	 */
	public function getSpawn(): Vector3{
		return new Vector3(128, 72, 128);
	}

	/**
	 * @param $chunkX
	 * @param $chunkZ
	 *
	 * @return void
	 */
	public function populateChunk(int $chunkX, int $chunkZ) : void{
	}
}
