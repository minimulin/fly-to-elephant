<?php

namespace FlyToElephant\Graph\Algorithms;

use FlyToElephant\Graph\GraphInterface;
use FlyToElephant\Graph\LeafInterface;
use FlyToElephant\Loggers\LoggerInterface;

/**
 * Интерфейс вершины
 */
interface AlgorithInterface
{
    public static function run(GraphInterface $graph, LeafInterface $beginLeafLeaf, LeafInterface $endLeafLeaf, LoggerInterface $logger = null);
}
