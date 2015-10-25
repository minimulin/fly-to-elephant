<?php

namespace FlyToElephant\Graph;

/**
 * Интерфейс графа
 */
interface GraphInterface
{
    public function addLeaf(LeafInterface $leaf);
    public function hasLeaf(LeafInterface $leaf);
    public function getLeafWithValue($value);
    public function getLeaves();
}
