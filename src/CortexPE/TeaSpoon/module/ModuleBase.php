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
 * @link   https://github.com/CortexPE/TeaSpoon
 *
 */

declare(strict_types=1);

namespace CortexPE\TeaSpoon\module;


abstract class ModuleBase {
	/** @var ModuleManager */
	private $moduleManager;

	final public function __construct(ModuleManager $moduleManager) {
		$this->moduleManager = $moduleManager;
	}

	public function getDataFolder(): string {
		return $this->moduleManager->getDataFolder();
	}

	/**
	 * @return ModuleManager
	 */
	final public function getModuleManager(): ModuleManager {
		return $this->moduleManager;
	}

	public function onLoad(): void {
	}

	public function onInitialize(): void {
	}

	public function onUnload(): void {
	}

	public function getLoadWeight(): int {
		return 0;
	}
}