<?php

namespace FlyToElephant\Graph;

/**
 *
 */
class WordLeaf
{
	protected $leaves;
	protected $value;
	protected $isFinal;
	protected $waveIndex;

	function __construct($value) {
		$this->leaves = [];
		$this->value = $value;
		$this->isFinal = false;
		$this->waveIndex = null;
	}

	public function addChildLeaf($wordLeaf)
	{
		if (!$this->hasChildLeaf($wordLeaf) && $wordLeaf != $this) {
			$this->leaves[] = $wordLeaf;
			if (!$wordLeaf->hasChildLeaf($this)) {
				$wordLeaf->addChildLeaf($this);
			}
		}
	}

	public function hasChildLeaf($wordLeaf)
	{
		return $this->hasLeafWithValue((string)$wordLeaf);
	}

	public function hasLeafWithValue($value)
	{
		foreach ($this->leaves as $leaf) {
			if ((string)$leaf == $value) {
				return true;
			}
		}

		return false;
	}

	public function __toString()
	{
		return $this->value;
	}

	public function isFinal()
	{
		return $this->isFinal;
	}

	public function setIsFinal($isFinal = true)
	{
		$this->isFinal = $isFinal;
	}

	public function setWaveIndex($waveIndex)
	{
		$this->waveIndex = $waveIndex;
	}

	public function getWaveIndex()
	{
		return $this->waveIndex;
	}

	public function setWaveIndexForChildren($waveIndex)
	{
		foreach ($this->leaves as $leaf) {
			if (is_null($leaf->getWaveIndex())) {
				$leaf->setWaveIndex($waveIndex);
			}
		}
	}

	public function getLeavesWithoutWaveIndex()
	{
		$leaves = [];
		foreach ($this->leaves as $leaf) {
			if (is_null($leaf->getWaveIndex())) {
				$leaves[] = $leaf;
			}
		}

		return $leaves;
	}

	public function getLeaves()
	{
		return $this->leaves;
	}

}
