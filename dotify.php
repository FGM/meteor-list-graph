<?php

use Fgm\MeteorListGraph\Tree;
use Pimple\Container as Pimple;
use Pimple\Psr11\Container as Psr11Container;

require_once __DIR__ . '/vendor/autoload.php';

$src = ($argc == 2)
  ? $argv[1]
  : __DIR__ . '/src/fixtures/ranking.txt';

$deps = file($src);
$dic = new Pimple();
$dic['logger'] = new \Psr\Log\NullLogger();

$tree = new Tree($deps, $dic);

$tree->grow();
$dot = $tree->flower();
echo $dot;
