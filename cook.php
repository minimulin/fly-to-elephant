#!/usr/bin/php
<?php

$loader = require_once __DIR__ . '/vendor/autoload.php';

$chain = FlyToElephant\FlyToElephant::magic('муха','слон');

die(var_dump($chain));