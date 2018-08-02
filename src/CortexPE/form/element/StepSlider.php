<?php
/*
 *               _ _
 *         /\   | | |
 *        /  \  | | |_ __ _ _   _
 *       / /\ \ | | __/ _` | | | |
 *      / ____ \| | || (_| | |_| |
 *     /_/    \_|_|\__\__,_|\__, |
 *                           __/ |
 *                          |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Altay
 *
 */
declare(strict_types=1);
namespace CortexPE\form\element;
use CortexPE\form\element\Dropdown;

class StepSlider extends Dropdown{
	public function getType() : string{
		return "step_slider";
	}
	public function serializeElementData() : array{
		return [
			"steps" => $this->options,
			"default" => $this->defaultOptionIndex
		];
	}
}
