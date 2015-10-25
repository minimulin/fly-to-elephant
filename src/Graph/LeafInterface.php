<?php

namespace FlyToElephant\Graph;

/**
 * Интерфейс вершины графа
 */
interface LeafInterface
{
    public function addChild(LeafInterface $leaf);
    public function hasChild(LeafInterface $leaf);
    public function hasChildWithValue($string);
    public function getChildren();
    public function __toString();
}
