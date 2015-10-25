<?php

namespace FlyToElephant\Graph\Algorithms;

use FlyToElephant\Graph\GraphInterface;
use FlyToElephant\Graph\WordLeafForLee;
use FlyToElephant\Graph\LeafInterface;
use FlyToElephant\Loggers\LoggerInterface;
use InvalidArgumentException;

/**
 * Алгоритм поиска кратчайшего пути: Алгоритм Ли (Волновой алгоритм, алгоритм волновой трассировки)
 */
class LeeAlgorithm implements AlgorithInterface
{
    /**
     * Возвращает массив со значениями вершин по кратчайшему пути из вершины начала до вершины назначения
     * @param  GraphInterface $graph      Граф
     * @param  WordLeafForLee  $beginLeaf Вершина начала
     * @param  WordLeafForLee  $endLeaf   Вершина назначения
     * @return mixed                      Возвращает массив со значениями вершин кратчайшего пути, либо null
     */
    public static function run(GraphInterface $graph, LeafInterface $beginLeaf, LeafInterface $endLeaf, LoggerInterface $logger = null)
    {
        //Не каждая реализация интерфейса LeafInterface нам подойдёт
        if (!($beginLeaf instanceof WordLeafForLee) || !($endLeaf instanceof WordLeafForLee)) {
            throw new InvalidArgumentException;
        }

        if ($logger) {
            $logger->log('Начало распространения волны');
        }
        
        if (static::waving($beginLeaf, $endLeaf, $logger)) {
            if ($logger) {
                $logger->log('Начало трассировки');
            }
            return static::backtrace($beginLeaf, $endLeaf, $logger);
        } else {
            return null;
        }
    }

    /**
     * Осуществляет реализацию распространения волны
     * @param  WordLeafForLee $beginLeaf Вершина начала
     * @param  WordLeafForLee $endLeaf   Вершина назначения
     * @return bool                      Успешность достижения вершины назначения
     */
    protected static function waving($beginLeaf, $endLeaf, $logger = null)
    {
        //Инициализация
        $waveIndex = 0;
        $beginLeaf->setWaveIndex($waveIndex);

        //Распространение волны по графу
        $leavesForNextIteration[] = $beginLeaf;

        $bContinue = true;
        while ($bContinue) {
            $waveIndex++;
            $currentIterationLeaves = $leavesForNextIteration;
            $leavesForNextIteration = [];

            //Если для итерации нет вершин, то вершина назначения оказалась недостижима. Кратчайший путь не найден
            if (count($currentIterationLeaves) == 0) {
                if ($logger) {
                    $logger->log('Распространение волны окончено');
                }
                return false;
            }

            //У каждой вершины проходим по детям и устанавливаем им индекс
            foreach ($currentIterationLeaves as $key => $leaf) {
                //Добавляем детей детей в следующую итерацию
                $leavesForNextIteration = array_merge($leavesForNextIteration, $leaf->getChildrenWithoutWaveIndex());
                $leaf->setWaveIndexForChildren($waveIndex);
                //Если среди детей у проходимой вершины замечена вершина назначения и у неё установлен индекс, то трассировка успешна
                if ($leaf->hasChildWithValue((string) $endLeaf) && $endLeaf->getWaveIndex()) {
                    if ($logger) {
                        $logger->log('Распространение волны окончено');
                    }
                    return true;
                }
            }
        }
    }

    /**
     * Осуществялет восстановление кратчайшего пути после распространения волны
     * @param  WordLeafForLee $beginLeaf Вершина начала
     * @param  WordLeafForLee $endLeaf   Вершина назначения
     * @return array                     Массив со значениями вершин кратчайшего пути
     */
    public static function backtrace($beginLeaf, $endLeaf, $logger = null)
    {
        //Восстановление пути
        $leafForNextIteration = $endLeaf;
        $waveIndex = $endLeaf->getWaveIndex() - 1;

        $backtrace = [(string) $endLeaf];

        while (true) {
            if ($waveIndex < 0) {
                break;
            }

            $currentInterationLeaf = $leafForNextIteration;
            //Идём вниз по фронту распространения волны и записываем детей, которых встречаем по пути
            foreach ($currentInterationLeaf->getChildren() as $leaf) {
                if ($leaf->getWaveIndex() == $waveIndex) {
                    $leafForNextIteration = $leaf;
                    $backtrace[] = (string) $leaf;
                    $waveIndex--;
                    break;
                }
            }
        }

        if ($logger) {
            $logger->log('Трассировка закончена');
        }
        return array_reverse($backtrace);
    }
}
