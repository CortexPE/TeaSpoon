<?php

/**
 *
 * MMP""MM""YMM               .M"""bgd
 * P'   MM   `7              ,MI    "Y
 *      MM  .gP"Ya   ,6"Yb.  `MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.
 *      MM ,M'   Yb 8)   MM    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM
 *      MM 8M""""""  ,pm9MM  .     `MM MM    M8 8M     M8 8M     M8 MM    MM
 *      MM YM.    , 8M   MM  Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM
 *    .JMML.`Mbmmd' `Moo9^Yo.P"Ybmmd"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML.
 *                                     MM
 *                                   .JMML.
 * This file is part of TeaSpoon.
 *
 * TeaSpoon is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeaSpoon is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TeaSpoon.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author CortexPE
 * @link https://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE;

use CortexPE\block\BlockManager;
use CortexPE\commands\CommandManager;
use CortexPE\entity\EntityManager;
use CortexPE\handlers\EnchantHandler;
use CortexPE\handlers\PacketHandler;
use CortexPE\item\{
	enchantment\Enchantment, ItemManager
};
use CortexPE\level\weather\Weather;
use CortexPE\plugin\AllAPILoaderManager;
use CortexPE\task\CheckPlayersTask;
use CortexPE\task\TickLevelsTask;
use CortexPE\tile\Tile;
use CortexPE\utils\TextFormat;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {
	private $splashes = [
		'Low-Calorie blend', "Don't panic! Have a cup of tea", "In England, Everything stops for tea", "ENGLAND IS MY CITY (not really)", "POWERED By Dubstep", "A E S T H E T H I C S", "WHO PUT THAT HERE?", "#BlameShoghi", "ERMAHGERD", "Written in PHP!", "This is a splash text.", "YOUR NAME", "ONE LOVE", "I KILLED THE SHERIFF... But not the deputy.", "Oops.", "rip.", "Fixed Typo!", "Fixed Typo! 2", "Fixed Typo! 2 FINAL", "Fixed Typo! 2 FINALFINAL", "Fixed Typo! 2 FINALFINALFINAL", "This splash text is a joke.", "Who made this?!", "How may I help you?", "asymmetricalacirtemmysa!", "SUPERCALIFRAGILISTICEXPIALIDOCIOUS!", "Well this exists.", "IE EXISTS TO DOWNLOAD CHROME!", "I'm sorry Dave. I'm afraid I can't do that.", "I might have killed it.", "PUNCHING TREES!", "Bug Fix", "Bug Fix 2", "Bug Fix 2 FINAL", "Bug Fix 2 FINALFINAL", "Bug Fix 2 FINALFINANFINAL", "We have VCS Systems. :P", "We have *crappy* VCS Systems. :P", ":shrug:", "Also try V A P O R W A V E", "Or S I M P S O N W A V E idk xD",
		"Fukkit FTW!!!",
		// Add more splashes fur fun. xD
	];
// Use static variables if it's going to be accessed by other Classes :)

	const CONFIG_VERSION = 9;

	/** @var string */
	public static $netherName = "nether";
	/** @var Level */
	public static $netherLevel;

	/** @var string */
	public static $endName = "ender";
	/** @var Level */
	public static $endLevel;

	/** @var Config */
	public static $config;

	/** @var bool */
	public static $lightningFire = false;
	/** @var string[] */
	public static $teleporting = [];
	/** @var int[] */
	public static $lastUses = [];
	/** @var int */
	public static $enderPearlCooldown = 2;
	/** @var array */
	public static $TEMPSkipCheck = [];
	/** @var array */
	public static $usingElytra = [];
	/** @var int */
	public static $ePearlDamage = 5;
	/** @var array */
	public static $TEMPAllowCheats = [];
	/** @var array */
	public static $lastEat = [];
	/** @var int */
	public static $chorusFruitCooldown = 2;
	/** @var bool */
	public static $registerVanillaEntities = true;
	/** @var bool */
	public static $registerVanillaEnchantments = true;
	/** @var bool */
	public static $registerDimensions = true;
	/** @var Weather[] */
	public static $weatherData = [];
	/** @var bool */
	public static $loadAllAPIs = false;
	/** @var bool */
	public static $weatherEnabled = true;
	/** @var int */
	public static $weatherMinTime = 6000;
	/** @var int */
	public static $weatherMaxTime = 12000;
	/** @var bool */
	public static $enableWeatherLightning = true;
	/** @var bool */
	public static $limitedCreative = false;
	/** @var array */
	public static $fishing = [];
	/** @var Entity[] | null[] */
	public static $fishingEntity = [];

	public function onLoad(){
		if(Utils::checkSpoon()){
			$this->getLogger()->error("This plugin is for PMMP only. It is meant to extend PMMP's functionality.");
			$this->getLogger()->error("The plugin will now disable itself to prevent any interference with the existing Spoon features.");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
		$this->getLogger()->info("Loading configuration...");
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

		self::$netherName = self::$config->get("netherName", "nether");
		self::$endName = self::$config->get("endName", "ender");
		self::$loadAllAPIs = self::$config->get("loadAllAPIs", false);
		self::$lightningFire = self::$config->get("lightningFire", false);
		self::$enderPearlCooldown = self::$config->get("enderPearlCooldown", 2);
		self::$ePearlDamage = self::$config->get("enderPearlDamage", 5);
		self::$chorusFruitCooldown = self::$config->get("chorusFruitCooldown", 2);
		self::$registerVanillaEntities = self::$config->get("registerVanillaEntities", true);
		self::$registerVanillaEnchantments = self::$config->get("registerVanillaEnchantments", true);
		self::$registerDimensions = self::$config->get("registerDimensions", true);
		self::$weatherEnabled = self::$config->get("weather", true);
		self::$weatherMinTime = self::$config->get("weatherMinTimeInTicks", 6000);
		self::$weatherMaxTime = self::$config->get("weatherMaxTimeInTicks", 12000);
		self::$enableWeatherLightning = self::$config->get("enableWeatherLightning", true);
		self::$limitedCreative = self::$config->get("limitedCreative", false);
	}

	public function onEnable(){
		$rm = $this->splashes[array_rand($this->splashes)];
		$stms = TextFormat::DARK_GREEN . '
		
MMP""MM""YMM              ' . TextFormat::GREEN . ' .M"""bgd                                        ' . TextFormat::DARK_GREEN . '
P\'   MM   `7             ' . TextFormat::GREEN . ' ,MI    "Y                                        ' . TextFormat::DARK_GREEN . '
     MM  .gP"Ya   ,6"Yb.  ' . TextFormat::GREEN . '`MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.  ' . TextFormat::DARK_GREEN . '
     MM ,M\'   Yb 8)   MM' . TextFormat::GREEN . '    `YMMNq. MM   `Wb 6W\'   `Wb 6W\'   `Wb MM    MM  ' . TextFormat::DARK_GREEN . '
     MM 8M""""""  ,pm9MM ' . TextFormat::GREEN . ' .     `MM MM    M8 8M     M8 8M     M8 MM    MM  ' . TextFormat::DARK_GREEN . '
     MM YM.    , 8M   MM  ' . TextFormat::GREEN . 'Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM  ' . TextFormat::DARK_GREEN . '
   .JMML.`Mbmmd\' `Moo9^Yo.' . TextFormat::GREEN . 'P"Ybmmd"  MMbmmd\'   `Ybmd9\'   `Ybmd9\'.JMML  JMML.' . TextFormat::GREEN . '
                                    MM                                     
                                  .JMML.  ' . TextFormat::YELLOW . $rm . TextFormat::RESET . '
Copyright (C) CortexPE 2017-Present
';

		$this->getLogger()->info("Loading..." . $stms);

		CommandManager::init();
		Enchantment::init();
		BlockManager::init();
		ItemManager::init();
		EntityManager::init();
		// LevelManager::init(); EXECUTED VIA EventListener
		if(self::$loadAllAPIs){
			AllAPILoaderManager::init();
		}
		Tile::init();
		if(self::$registerDimensions){
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new CheckPlayersTask($this), 10);
		}
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new PacketHandler($this), $this);
		if(self::$registerVanillaEnchantments){
			$this->getServer()->getPluginManager()->registerEvents(new EnchantHandler($this), $this);
		}
		$ver = self::$config->get("version");
		if(self::$weatherEnabled){
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new TickLevelsTask($this), 1);
		}
		$this->getLogger()->info("TeaSpoon is distributed under the AGPL License");


		if($ver === null || $ver === false || $ver < self::CONFIG_VERSION){
			$this->getLogger()->critical("Your configuration file is Outdated! Keep a backup of it and delete the outdated file.");
		}elseif($ver > self::CONFIG_VERSION){
			$this->getLogger()->critical("Your configuration file is from a higher version of TeaSpoon! Please update the plugin from https://github.com/CortexPE/TeaSpoon");
		}
	}
}