<?php

declare(strict_types = 1);

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

// Modded by @CortexPE to make it more realistic + performance improvements

namespace CortexPE\level\weather;

use CortexPE\Main;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;

class Weather {

	/** @var int */
	public const
		CLEAR = 0,
		SUNNY = 0,
		RAIN = 1,
		RAINY = 1,
		RAINY_THUNDER = 2,
		THUNDER = 3;

	private $level;
	private $weatherNow = 0;
	private $strength1;
	private $strength2;
	private $duration;
	private $canCalculate = true;

	/** @var Vector3 */
	private $temporalVector = null;

	private $lastUpdate = 0;

	private $randomWeatherData = [
		self::CLEAR,
		self::RAIN,
		self::RAINY_THUNDER,
	];

	/**
	 * Weather constructor.
	 *
	 * @param Level $level
	 * @param int $duration
	 */
	public function __construct(Level $level, $duration = 1200){
		$this->level = $level;
		$this->weatherNow = self::SUNNY;
		$this->duration = $duration;
		$this->lastUpdate = $level->getServer()->getTick();
		$this->temporalVector = new Vector3(0, 0, 0);
	}

	/**
	 * @param $weather
	 *
	 * @return int
	 */
	public static function getWeatherFromString($weather){
		if(is_int($weather)){
			if($weather <= 3){
				return $weather;
			}

			return -1; // invalid weather
		}
		switch(strtolower($weather)){
			case "clear":
			case "sunny":
			case "fine":
				return self::SUNNY;
			case "rain":
			case "rainy":
				return self::RAINY;
			case "thunder":
				return self::THUNDER;
			case "rain_thunder":
			case "rainy_thunder":
			case "storm":
				return self::RAINY_THUNDER;
			default:
				return -1;
		}
	}

	/**
	 * @param bool $canCalc
	 */
	public function setCanCalculate(bool $canCalc){
		$this->canCalculate = $canCalc;
	}

	/**
	 * @param $currentTick
	 */
	public function calcWeather($currentTick){
		if($this->canCalculate()){
			$tickDiff = $currentTick - $this->lastUpdate;
			$this->duration -= $tickDiff;

			if($this->duration <= 0){
				$duration = mt_rand(
					min(Main::$weatherMinTime, Main::$weatherMaxTime),
					max(Main::$weatherMinTime, Main::$weatherMaxTime));

				if($this->weatherNow === self::SUNNY){
					$weather = $this->randomWeatherData[array_rand($this->randomWeatherData)];
					$this->setWeather($weather, $duration);
				}else{
					$weather = self::SUNNY;
					$this->setWeather($weather, $duration);
				}
			}
			if(($this->weatherNow == self::RAINY_THUNDER or $this->weatherNow == self::THUNDER) and is_int($this->duration / 200)){
				$players = $this->level->getPlayers();
				if(count($players) > 0){
					$p = $players[array_rand($players)];
					$x = $p->x + mt_rand(-64, 64);
					$z = $p->z + mt_rand(-64, 64);
					$y = $this->level->getHighestBlockAt((int)$x, (int)$z);

					if(Main::$enableWeatherLightning){
						$nbt = Entity::createBaseNBT(new Vector3($x, $y, $z));
						$lightning = Entity::createEntity("Lightning", $this->level, $nbt);
						$lightning->spawnToAll();
					}
				}
			}
			$this->lastUpdate = $currentTick;
		}
	}

	/**
	 * @return bool
	 */
	public function canCalculate(): bool{
		return $this->canCalculate;
	}

	/**
	 * @param int $wea
	 * @param int $duration
	 */
	public function setWeather(int $wea, int $duration = 12000){
		$this->weatherNow = $wea;
		$this->strength1 = mt_rand(90000, 110000); //If we're clearing the weather, it doesn't matter what strength values we set
		$this->strength2 = mt_rand(30000, 40000);
		$this->duration = $duration;
		$this->sendWeatherToAll();
	}

	public function sendWeatherToAll(){
		foreach($this->level->getPlayers() as $player){
			$this->sendWeather($player);
		}
	}

	/**
	 * @param Player $p
	 */
	public function sendWeather(Player $p){
		$pks = [
			new LevelEventPacket(),
			new LevelEventPacket(),
		];

		//Set defaults. These will be sent if the case statement defaults.
		$pks[0]->evid = LevelEventPacket::EVENT_STOP_RAIN;
		$pks[0]->data = $this->strength1;
		$pks[1]->evid = LevelEventPacket::EVENT_STOP_THUNDER;
		$pks[1]->data = $this->strength2;

		switch($this->weatherNow){
			//If the weather is not clear, overwrite the packet values with these
			case self::RAIN:
				$pks[0]->evid = LevelEventPacket::EVENT_START_RAIN;
				$pks[0]->data = $this->strength1;
				break;
			case self::RAINY_THUNDER:
				$pks[0]->evid = LevelEventPacket::EVENT_START_RAIN;
				$pks[0]->data = $this->strength1;
				$pks[1]->evid = LevelEventPacket::EVENT_START_THUNDER;
				$pks[1]->data = $this->strength2;
				break;
			case self::THUNDER:
				$pks[1]->evid = LevelEventPacket::EVENT_START_THUNDER;
				$pks[1]->data = $this->strength2;
				break;
			default:
				break;
		}

		foreach($pks as $pk){
			$p->dataPacket($pk);
		}
	}

	/**
	 * @return array
	 */
	public function getRandomWeatherData(): array{
		return $this->randomWeatherData;
	}

	/**
	 * @param array $randomWeatherData
	 */
	public function setRandomWeatherData(array $randomWeatherData){
		$this->randomWeatherData = $randomWeatherData;
	}

	/**
	 * @return bool
	 */
	public function isSunny(): bool{
		if(!$this->canCalculate){
			return false;
		}

		return $this->getWeather() === self::SUNNY;
	}

	/**
	 * @return int
	 */
	public function getWeather(): int{
		if(!$this->canCalculate){
			return self::SUNNY;
		}

		return $this->weatherNow;
	}

	/**
	 * @return bool
	 */
	public function isRainy(): bool{
		if(!$this->canCalculate){
			return false;
		}

		return $this->getWeather() === self::RAINY;
	}

	/**
	 * @return bool
	 */
	public function isRainyThunder(): bool{
		if(!$this->canCalculate){
			return false;
		}

		return $this->getWeather() === self::RAINY_THUNDER;
	}

	/**
	 * @return bool
	 */
	public function isThunder(): bool{
		if(!$this->canCalculate){
			return false;
		}

		return $this->getWeather() === self::THUNDER;
	}

	/**
	 * @return array
	 */
	public function getStrength(): array{
		return [$this->strength1, $this->strength2];
	}

}