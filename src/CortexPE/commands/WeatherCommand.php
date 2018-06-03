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

namespace CortexPE\commands;

use CortexPE\level\weather\Weather;
use CortexPE\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\lang\TranslationContainer;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class WeatherCommand extends VanillaCommand {

	/**
	 * WeatherCommand constructor.
	 *
	 * @param string $name
	 */
	public function __construct($name){
		parent::__construct(
			$name,
			"Changes the Weather",
			"/weather [level] < get | clear | sunny | rain | rainy_thunder | thunder >"
		);
		$this->setPermission("pocketmine.command.weather");
	}

	/**
	 * @param CommandSender $sender
	 * @param string $currentAlias
	 * @param array $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) < 1){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

			return false;
		}

		if($sender instanceof Player){
			if($args[0] == "get"){
				switch(Main::$weatherData[$sender->getLevel()->getId()]->getWeather()){
					case 0:
						$sender->sendMessage("Weather: Clear");

						return true;
					case 1:
						$sender->sendMessage("Weather: Rainy");

						return true;
					case 2:
						$sender->sendMessage("Weather: Rainy Thunder");

						return true;
					case 3:
						$sender->sendMessage("Weather: Thunder");

						return true;
				}
			}
			$wea = Weather::getWeatherFromString($args[0]);
			if(!isset($args[1])) $duration = mt_rand(
				min(Main::$weatherMinTime, Main::$weatherMaxTime),
				max(Main::$weatherMinTime, Main::$weatherMaxTime));
			else $duration = (int)$args[1];
			if($wea >= 0 and $wea <= 3){
				Main::$weatherData[$sender->getLevel()->getId()]->setWeather($wea, $duration);
				$sender->sendMessage("Weather Successfully changed on " . $sender->getLevel()->getName());

				return true;
			}else{
				$sender->sendMessage(TextFormat::RED . "Invalid Weather");

				return false;
			}
		}

		if(count($args) < 2){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

			return false;
		}

		$level = $sender->getServer()->getLevelByName($args[0]);
		if(!$level instanceof Level){
			$sender->sendMessage(TextFormat::RED . "Couldn't find level: " . $args[0]);

			return false;
		}
		if($args[1] == "get"){
			switch(Main::$weatherData[$level->getId()]->getWeather()){
				case 0:
					$sender->sendMessage("Weather: Clear");

					return true;
				case 1:
					$sender->sendMessage("Weather: Rainy");

					return true;
				case 2:
					$sender->sendMessage("Weather: Rainy Thunder");

					return true;
				case 3:
					$sender->sendMessage("Weather: Thunder");

					return true;
			}
		}

		$wea = Weather::getWeatherFromString($args[1]);
		if(!isset($args[1])) $duration = mt_rand(
			min(Main::$weatherMinTime, Main::$weatherMaxTime),
			max(Main::$weatherMinTime, Main::$weatherMaxTime));
		else $duration = (int)$args[1];
		if($wea >= 0 and $wea <= 3){
			Main::$weatherData[$level->getId()]->setWeather($wea, $duration);
			$sender->sendMessage("Weather Successfully changed on " . $level->getName());

			return true;
		}else{
			$sender->sendMessage(TextFormat::RED . "Invalid Weather");

			return false;
		}
	}
}
