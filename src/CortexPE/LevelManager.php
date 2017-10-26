<?php

namespace CortexPE;

use CortexPE\level\generator\ender\Ender;
use CortexPE\level\generator\hell\Nether;
use pocketmine\level\generator\Generator;
use pocketmine\Server as PMServer;

class LevelManager {
	public static function init(){
		self::registerGenerators();
		self::loadAndGenerateLevels();
	}

	public static function registerGenerators(){
		Generator::addGenerator(Nether::class, "nether");
		Generator::addGenerator(Ender::class, "ender");
	}

	public static function loadAndGenerateLevels(){
		if(!PMServer::getInstance()->loadLevel(Main::$netherName)){
			PMServer::getInstance()->generateLevel(Main::$netherName, time(), Generator::getGenerator("nether"));
		}
		Main::$netherLevel = PMServer::getInstance()->getLevelByName(Main::$netherName);


		if(!PMServer::getInstance()->loadLevel(Main::$endName)){
			PMServer::getInstance()->generateLevel(Main::$endName, time(), Generator::getGenerator("ender"));
		}
		Main::$endLevel = PMServer::getInstance()->getLevelByName(Main::$endName);
	}
}