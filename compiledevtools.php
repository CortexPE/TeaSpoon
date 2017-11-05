<?php
$phar = new Phar(__DIR__.'/PocketMine-MP/plugins/DevTools.phar');
$phar->buildFromDirectory(__DIR__.'/PocketMine-DevTools');