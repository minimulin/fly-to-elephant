<?php

namespace FlyToElephant\Graph;

/**
 * Вершина графа для волнового алгоритма
 */
class WordLeafForLee implements LeafInterface
{
    protected $leaves;
    protected $value;
    protected $isFinal;
    protected $waveIndex;

    public function __construct($value)
    {
        $this->leaves = [];
        $this->value = $value;
        $this->isFinal = false;
        $this->waveIndex = null;
    }

    /**
     * Добавляет к вершине дочернюю вершину
     * @param LeafInterface $wordLeaf Дочерняя вершина
     */
    public function addChild(LeafInterface $wordLeaf)
    {
        if (!$this->hasChild($wordLeaf) && (string) $wordLeaf != (string) $this) {
            $this->leaves[] = $wordLeaf;
            if (!$wordLeaf->hasChild($this)) {
                $wordLeaf->addChild($this);
            }
        }
    }

    /**
     * Проверяет наличие вершины среди детей
     * @param  LeafInterface $wordLeaf Искома вершина
     * @return boolean                 Наличие вершины в списке детей
     */
    public function hasChild(LeafInterface $wordLeaf)
    {
        return $this->hasChildWithValue((string) $wordLeaf);
    }

    /**
     * Осуществляет поиск вершины среди детей по значению
     * @param  string  $value Значение
     * @return boolean        Наличие вершины среди детей
     */
    public function hasChildWithValue($value)
    {
        foreach ($this->leaves as $leaf) {
            if ((string) $leaf == $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает массив дочерних вершин
     * @return array Дочерние вершины
     */
    public function getChildren()
    {
        return $this->leaves;
    }

    /**
     * Реализация магического метода
     * @return string Значение вершины
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * Устанавливает для вершины индекс при распространении волны
     * @param int $waveIndex Индекс
     */
    public function setWaveIndex($waveIndex)
    {
        $this->waveIndex = $waveIndex;
    }

    /**
     * Возвращает индекс распространения волны
     * @return int Индекс
     */
    public function getWaveIndex()
    {
        return $this->waveIndex;
    }

    /**
     * Устанавливает для всех детей вершины соответствующий индекс
     * @param int $waveIndex Индекс распространения волны
     */
    public function setWaveIndexForChildren($waveIndex)
    {
        foreach ($this->leaves as $leaf) {
            if (is_null($leaf->getWaveIndex())) {
                $leaf->setWaveIndex($waveIndex);
            }
        }
    }

    /**
     * Возвращает дочерние вершины, у которых не установлен индекс распространения.
     * Оптимизация поиска кратчайшего пути. Таким образом мы избавляемся от огромного количества ненужных итераций
     * @return array Дочерние вершины без индекса распространения волны
     */
    public function getChildrenWithoutWaveIndex()
    {
        $leaves = [];
        foreach ($this->leaves as $leaf) {
            if (is_null($leaf->getWaveIndex())) {
                $leaves[] = $leaf;
            }
        }

        return $leaves;
    }
}
