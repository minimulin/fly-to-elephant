<?php

namespace FlyToElephant\Graph;

/**
 *
 */
class WordGraph
{
    protected $leaves;

    public function __construct()
    {
        $this->leaves = [];
    }

    public function addLeaf(WordLeaf $leaf)
    {
        if (!$this->hasLeaf($leaf)) {
            $this->leaves[] = $leaf;
        }
    }

    public function hasLeaf(WordLeaf $leaf)
    {
        return !!$this->getLeafWithValue((string)$leaf);
    }

    public function getLeafWithValue($value)
    {
        foreach ($this->leaves as $leaf) {
            if ((string)$leaf == $value) {
                return $leaf;
            }
        }

        return false;
    }

    public function addWord($word)
    {
        if (!$this->getLeafWithValue($word)) {
            $leaf = new WordLeaf($word);
            $this->addLeaf($leaf);
            return $leaf;
        } else {
            return false;
        }
    }

    public function getLeaves()
    {
        return $this->leaves;
    }

    public function getWords()
    {
    	$words = [];
    	foreach ($this->leaves as $leaf) {
    		$words[] = (string)$leaf;
    	}

    	return $words;
    }

    public function addWordFor($parentLeaf, $word)
    {
    	$leaf = $this->getLeafWithValue($word);
        if (!$leaf) {
            $leaf = new WordLeaf($word);
        }

        $this->addLeaf($leaf);
        $parentLeaf->addChildLeaf($leaf);
    }
}
