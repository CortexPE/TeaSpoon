<?php

declare(strict_types = 1);

namespace CortexPE\level\particle;

use pocketmine\level\particle\GenericParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

class SpellParticle extends GenericParticle {
	/**
	 * SpellParticle constructor.
	 *
	 * @param Vector3 $pos
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @param int $a
	 */
	public function __construct(Vector3 $pos, $r = 0, $g = 0, $b = 0, $a = 255){
		parent::__construct($pos, LevelEventPacket::EVENT_PARTICLE_SPLASH, (($a & 0xff) << 24) | (($r & 0xff) << 16) | (($g & 0xff) << 8) | ($b & 0xff));
	}

	/**
	 * @return LevelEventPacket
	 */
	public function encode(){
		$pk = new LevelEventPacket();
		$pk->evid = LevelEventPacket::EVENT_PARTICLE_SPLASH;
		$pk->position = new Vector3($this->x, $this->y, $this->z);
		$pk->data = $this->data;

		return $pk;
	}
}