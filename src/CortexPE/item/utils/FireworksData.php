<?php

/*
 * Credits to @thebigsmileXD (XenialDan)
 * Original Repository: https://github.com/thebigsmileXD/fireworks
 * Ported to TeaSpoon as TeaSpoon overrides the fireworks item (as Elytra Booster)
 * Licensed under the MIT License (January 1, 2018)
 * */

declare(strict_types = 1);

namespace CortexPE\item\utils;


class FireworksData {
	/** @var int */
	public $flight = 1;
	/** @var FireworksExplosion[] */
	public $explosions = [];
}