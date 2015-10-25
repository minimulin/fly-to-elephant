#!/usr/bin/php
<?php

$loader = require_once __DIR__ . '/vendor/autoload.php';

$chain = FlyToElephant\FlyToElephant::magic('муха','слон');

if (is_array($chain)) {
	printf("Цепочка найдена и содержит %d слов: %s\n", count($chain), implode('->', $chain));
} else {
	echo "Решение не найдено\n";
}
