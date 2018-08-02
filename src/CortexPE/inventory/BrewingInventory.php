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

namespace CortexPE\inventory;


use CortexPE\tile\BrewingStand;
use pocketmine\inventory\ContainerInventory;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class BrewingInventory extends ContainerInventory {
	public const SLOT_INGREDIENT = 0;
	public const SLOT_LEFT = 1;
	public const SLOT_MIDDLE = 2;
	public const SLOT_RIGHT = 3;
	public const SLOT_FUEL = 4;
	/** @var BrewingStand */
	protected $holder;

	public function __construct(BrewingStand $holder, array $items = [], int $size = \null, string $title = \null){
		parent::__construct($holder, $items, $size, $title);
	}

	public function getDefaultSize(): int{
		return 5;
	}

	public function getName(): string{
		return "Brewing";
	}

	public function getNetworkType(): int{
		return WindowTypes::BREWING_STAND;
	}

	public function onSlotChange(int $index, Item $before, bool $send): void{
		$this->holder->scheduleUpdate();
		parent::onSlotChange($index, $before, $send);
	}

	public function getIngredient(): Item{
		return $this->getItem(self::SLOT_INGREDIENT);
	}

	public function setIngredient(Item $item): void{
		$this->setItem(self::SLOT_INGREDIENT, $item, true);
	}

	/**
	 * @return Item[]
	 */
	public function getPotions(): array{
		$return = [];
		for($i = 1; $i <= 3; $i++){
			$return[] = $this->getItem($i);
		}

		return $return;
	}

	public function onClose(Player $who): void{
		parent::onClose($who);
		$this->holder->saveNBT();
	}

	public function onOpen(Player $who): void{
		parent::onOpen($who);
		$this->holder->loadBottles();
	}

	public function getFuel(): Item{
		return $this->getItem(self::SLOT_FUEL);
	}

	public function setFuel(Item $fuel): void{
		$this->setItem(self::SLOT_FUEL, $fuel);
	}
}
