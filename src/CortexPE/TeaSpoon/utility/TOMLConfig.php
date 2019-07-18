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

namespace CortexPE\TeaSpoon\utility;


use function array_keys;
use function implode;
use function is_array;
use function uniqid;
use function var_dump;
use Yosymfony\Toml\Toml;
use Yosymfony\Toml\TomlBuilder;
use function array_diff_key;
use function array_merge;
use function file_exists;
use function file_put_contents;

class TOMLConfig {
	/** @var string */
	private $fileName;
	/** @var string[] */
	private $header = [];
	/** @var array */
	private $data = [];

	public function __construct(string $fileName, array $defaults, array $header = []) {
		$this->fileName = $fileName;
		$this->header = $header;
		if(file_exists($fileName)) {
			$this->data = array_merge($defaults, ($exist = (Toml::parseFile($fileName) ?? [])));
			if($this->data === $exist) {
				return;
			}
			$this->data = array_merge($exist, array_diff_key($this->data, $exist));
		}
		$this->save();
	}

	public function getData(): array {
		return $this->data;
	}

	public function get(string $key) {
		return $this->data[$key] ?? null;
	}

	public function set(string $key, $value): void {
		$this->data[$key] = $value;
	}

	/**
	 * @param string[] $header
	 */
	public function setHeader(array $header): void {
		$this->header = $header;
	}

	/**
	 * @return string[]
	 */
	public function getHeader(): array {
		return $this->header;
	}

	private static function crawl(TomlBuilder $builder, array $dat, array $keys = []) {
		foreach($dat as $k => $v){
			if(is_array($v)){
				if(range(0, count($v)) === array_keys($v)){ // list
					$builder->addArrayOfTable(implode(".", $keys));
				}else{ // dict
					$builder->addTable(implode(".", $keys));
				}
				$keys[] = uniqid("test");
				self::crawl($builder, $v, $keys);
			} else {
				$builder->addValue((string)$k, $v);
			}
		}
	}

	public function save():void {
		$builder = new TomlBuilder();
		foreach($this->header as $line) {
			$builder->addComment($line);
		}
		foreach($this->data as $k => $v) {
			$builder->addValue($k, $v);
		}
		self::crawl($builder, $this->data);
		file_put_contents($this->fileName, $builder->getTomlString());
	}
}