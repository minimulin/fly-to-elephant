<?php

namespace FlyToElephant\Graph;

use FlyToElephant\Loggers\LoggerInterface;

/**
 * Граф слов
 */
class WordGraph implements GraphInterface
{
    protected $leaves;

    /**
     * @param array|null $words Массив слов
     */
    public function __construct(array $words = null, LoggerInterface $logger = null)
    {
        $this->leaves = [];
        $this->logger = $logger;
        if ($words) {
            $this->buildGraph($words);
        }
    }

    /**
     * Добавляет вершину к графу
     * @param LeafInterface $leaf Вершина
     */
    public function addLeaf(LeafInterface $leaf)
    {
        if (!$this->hasLeaf($leaf)) {
            $this->leaves[] = $leaf;
        }
    }

    /**
     * Осуществляет проверку наличия вершины в графе
     * @param  LeafInterface $leaf Вершина
     * @return boolean              Наличие вершины в графе
     */
    public function hasLeaf(LeafInterface $leaf)
    {
        return !!$this->getLeafWithValue((string) $leaf);
    }

    /**
     * Возврашает вершину по её значению
     * @param  string $value Искомое значение
     * @return mixed         Вершина
     */
    public function getLeafWithValue($value)
    {
        foreach ($this->leaves as $leaf) {
            if ((string) $leaf == $value) {
                return $leaf;
            }
        }

        return false;
    }

    /**
     * Создаёт вершину со значением
     * @param string $word Значение
     */
    public function addWord($word)
    {
        if (!$this->getLeafWithValue($word)) {
            $leaf = new WordLeafForLee($word);
            $this->addLeaf($leaf);
            return $leaf;
        } else {
            return false;
        }
    }

    /**
     * Возвращает массив вершин графа
     * @return array Вершины графа
     */
    public function getLeaves()
    {
        return $this->leaves;
    }

    /**
     * Строит граф по массиву слов
     * @param  array $words Массив слов
     */
    protected function buildGraph(array $words)
    {
        if ($this->logger) {
            $this->logger->log('Идет заполнение графа');
        }

        foreach ($words as $word) {
            $this->addWord($word);
        }

        if ($this->logger) {
            $this->logger->log('Определение и построение связей');
        }

        foreach ($this->getLeaves() as $word1) {
            foreach ($this->getLeaves() as $word2) {
                if (((string) $word1 == (string) $word2) || $word1->hasChild($word2)) {
                    continue;
                }

                if (static::diffString((string) $word1, (string) $word2) == 1) {
                    $word1->addChild($word2);
                }
            }
        }

        if ($this->logger) {
            $this->logger->log('Граф построен');
        }
    }

    /**
     * Ищет количество различающихся символов в двуз строках
     * @param  string $str1 Первая строка
     * @param  string $str2 Вторая строка
     * @return int          Количество различающихся символов
     */
    public static function diffString($str1, $str2)
    {
        $str1AsArray = static::mbStrSplit($str1);
        $str2AsArray = static::mbStrSplit($str2);
        $diff = 0;

        for ($i = 0; $i < count($str1AsArray); $i++) {
            if ($str1AsArray[$i] !== $str2AsArray[$i]) {
                $diff++;
            }
        }
        return $diff;
    }

    /**
     * Разделяет мультибайт-строку на массив символов
     * @param  string $str Строка
     * @return array      Массив символов строки
     */
    public static function mbStrSplit($str)
    {
        preg_match_all('#.{1}#uis', $str, $out);
        return $out[0];
    }
}
