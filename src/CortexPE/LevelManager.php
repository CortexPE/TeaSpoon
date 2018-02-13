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

use CortexPE\level\generator\{
	ender\Ender, hell\Nether, VoidGenerator
};
use pocketmine\level\generator\Generator;
use pocketmine\Server as PMServer;

class LevelManager {
	public static $loaded = false;

	public static function init(){
		if(!self::$loaded){
			self::registerGenerators();
			self::loadAndGenerateLevels();
			self::$loaded = true;
		}
	}

	public static function registerGenerators(){
		Generator::addGenerator(Nether::class, "nether");
		Generator::addGenerator(Ender::class, "ender");
		Generator::addGenerator(VoidGenerator::class, "void");
	}

	public static function loadAndGenerateLevels(){
		if(Main::$registerDimensions){
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
}
