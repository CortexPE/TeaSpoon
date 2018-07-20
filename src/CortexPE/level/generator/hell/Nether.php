<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

declare(strict_types = 1);

namespace CortexPE\level\generator\hell;

use CortexPE\level\generator\populator\GroundFire;
use CortexPE\level\generator\populator\NetherGlowStone;
use CortexPE\level\generator\populator\NetherLava;
use CortexPE\level\generator\populator\NetherOre;
use pocketmine\block\Block;
use pocketmine\block\Gravel;
use pocketmine\block\Lava;
use pocketmine\block\NetherQuartzOre;
use pocketmine\block\SoulSand;
use pocketmine\level\ChunkManager;
use pocketmine\level\biome\Biome;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\level\generator\object\OreType;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3 as Vector3;
use pocketmine\utils\Random;

class Nether extends \pocketmine\level\generator\hell\Nether {

	private static $GAUSSIAN_KERNEL = null;
	private static $SMOOTH_SIZE = 2;
	/** @var Populator[] */
	private $populators = [];
	/** @var ChunkManager */
	protected $level;
	/** @var Random */
	protected $random;
	private $waterHeight = 32;
	private $emptyHeight = 64;
	private $emptyAmplitude = 1;
	private $density = 0.5;
	/** @var Populator[] */
	private $generationPopulators = [];
	/** @var Simplex */
	private $noiseBase;
	private $bedrockDepth = 5;

	/**
	 * Nether constructor.
	 *
	 * @param array $options
	 */
	public function __construct(array $options = []){
		if(self::$GAUSSIAN_KERNEL === null){
			self::generateKernel();
		}
	}

	private static function generateKernel(){
		self::$GAUSSIAN_KERNEL = [];

		$bellSize = 1 / self::$SMOOTH_SIZE;
		$bellHeight = 2 * self::$SMOOTH_SIZE;

		for($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; ++$sx){
			self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] = [];

			for($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; ++$sz){
				$bx = $bellSize * $sx;
				$bz = $bellSize * $sz;
				self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE][$sz + self::$SMOOTH_SIZE] = $bellHeight * exp(-($bx * $bx + $bz * $bz) / 2);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return "TeaNether";
	}

	/**
	 * @return int
	 */
	public function getWaterHeight(): int{
		return $this->waterHeight;
	}

	/**
	 * @return array
	 */
	public function getSettings(): array{
		return [];
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
		$this->random->setSeed($this->level->getSeed());
		$this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 64);
		$this->random->setSeed($this->level->getSeed());

		$ores = new NetherOre();
		$ores->setOreTypes([
			new OreType(new NetherQuartzOre(), 20, 16, 0, 128),
			new OreType(new SoulSand(), 5, 64, 0, 128),
			new OreType(new Gravel(), 5, 64, 0, 128),
			new OreType(new Lava(), 1, 16, 0, $this->waterHeight),
		]);
		$this->populators[] = $ores;
		$this->populators[] = new NetherGlowStone();
		$groundFire = new GroundFire();
		$groundFire->setBaseAmount(1);
		$groundFire->setRandomAmount(1);
		$this->populators[] = $groundFire;
		$lava = new NetherLava();
		$lava->setBaseAmount(0);
		$lava->setRandomAmount(2);
		$this->populators[] = $lava;
	}

	/**
	 * @param $chunkX
	 * @param $chunkZ
	 *
	 * @return void
	 */
	public function generateChunk(int $chunkX, int $chunkZ) : void{
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

		$noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

		$chunk = $this->level->getChunk($chunkX, $chunkZ);

		for($x = 0; $x < 16; ++$x){
			for($z = 0; $z < 16; ++$z){

				$biome = Biome::getBiome(Biome::HELL);
				$chunk->setBiomeId($x, $z, $biome->getId());

				$chunk->setBlockId($x, 0, $z, Block::BEDROCK);
				$chunk->setBlockId($x, 127, $z, Block::BEDROCK);

				for($y = 0; $y < 128; ++$y){
					$noiseValue = (abs($this->emptyHeight - $y) / $this->emptyHeight) * $this->emptyAmplitude - $noise[$x][$z][$y];
					$noiseValue -= 1 - $this->density;

					if($noiseValue > 0){
						$chunk->setBlockId($x, $y, $z, Block::NETHERRACK);
					}elseif($y <= $this->waterHeight){
						$chunk->setBlockId($x, $y, $z, Block::STILL_LAVA);
						$chunk->setBlockLight($x, $y, $z, Block::get(Block::STILL_LAVA)->getLightLevel());
					}

					if($y <= $this->bedrockDepth){
						if($this->random->nextRange(1,5) == 1){
							$chunk->setBlockId($x, $y, $z, Block::BEDROCK);
							$chunk->setBlockId($x, 127 - $y, $z, Block::BEDROCK);
						}
					}
				}
			}
		}

		foreach($this->generationPopulators as $populator){
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
	}

	/**
	 * @param $chunkX
	 * @param $chunkZ
	 *
	 * @return void
	 */
	public function populateChunk(int $chunkX, int $chunkZ) : void{
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
		foreach($this->populators as $populator){
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}

		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		$biome = Biome::getBiome($chunk->getBiomeId(7, 7));
		$biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
	}

	/**
	 * @return Vector3
	 */
	public function getSpawn(): Vector3{
		return new Vector3(127.5, 128, 127.5);
	}

}
