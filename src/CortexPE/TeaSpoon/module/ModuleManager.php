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
 * @author CortexPE, and contributors
 * @link https://github.com/CortexPE/TeaSpoon
 *
 */

declare(strict_types=1);


namespace CortexPE\TeaSpoon\module;


use CortexPE\TeaSpoon\exception\ModuleException;
use CortexPE\TeaSpoon\Main;
use function is_dir;
use function mkdir;
use pocketmine\event\Listener;
use function is_subclass_of;
use function str_replace;
use function trim;

class ModuleManager {
	/** @var Main */
	protected $plugin;
	/** @var ModuleBase[] */
	protected $modules = [];
	/** @var string */
	protected $dataFolder;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		if(!is_dir(($this->dataFolder = $plugin->getDataFolder() . "/modules/"))){
			mkdir($this->dataFolder);
		}
	}

	/**
	 * @return string
	 */
	public function getDataFolder(): string {
		return $this->dataFolder;
	}

	/**
	 * @param string $className
	 *
	 * @throws ModuleException
	 */
	public function loadModule(string $className): void {
		if(!is_subclass_of($className, ModuleBase::class)) {
			throw new ModuleException("Module must be a subclass of " . ModuleBase::class);
		}
		if($this->hasModule($className)) {
			throw new ModuleException("Module already in use");
		}
		/** @var ModuleBase $mod */
		$mod = $this->modules[self::cleanupModuleCName($className)] = new $className($this);
		if($mod instanceof Listener) {
			$this->plugin->getServer()->getPluginManager()->registerEvents($mod, $this->plugin);
		}
		$mod->onLoad();
	}

	/**
	 * @param string $className
	 *
	 * @throws ModuleException
	 */
	public function unLoadModule(string $className): void {
		if(!$this->hasModule($className)) {
			throw new ModuleException("Module not in use");
		}
		$this->modules[($i = self::cleanupModuleCName($className))]->onUnload();
		unset($this->modules[$i]);
	}

	public function hasModule(string $className):bool {
		return isset($this->modules[self::cleanupModuleCName($className)]);
	}

	private static function cleanupModuleCName(string $className): string {
		return str_replace("/", "\\", trim($className, "\\/"));
	}

	public function getModule(string $className): ?ModuleBase {
		if($this->hasModule($className)) {
			return $this->modules[self::cleanupModuleCName($className)];
		}

		return null;
	}

	/**
	 * @internal
	 */
	public function enableModules(): void {
		foreach($this->modules as $name => $module){
			$module->onInitialize();
		}
	}

	/**
	 * @throws ModuleException
	 * @internal
	 */
	public function disableModules(): void {
		foreach($this->modules as $name => $module){
			$this->unLoadModule($name);
		}
	}
}