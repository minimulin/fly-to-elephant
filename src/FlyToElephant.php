<?php

namespace FlyToElephant;

use FlyToElephant\DataHandlers\SQLite3DataHandler;
use FlyToElephant\Graph\WordGraph;

/**
 *
 */
class FlyToElephant
{

    public static function magic($fly, $elephant)
    {
        $dataWorker = SQLite3DataHandler::getInstance('dictionary.sqlite');
        $overallWordsCount = $dataWorker->sqliteFetchArray('SELECT COUNT(*) as count FROM words WHERE word LIKE "____"')[0]['count'];

        $mode = 1;

        $graph = new WordGraph();
        $begin = $graph->addWord($fly);
        $wordCount = 0;

        $iterations = 0;
        $count = 0;

        while (count($graph->getLeaves()) != $count) {
            $count = $graph->getLeaves();

            foreach ($graph->getLeaves() as $leaf) {
                if (!$leaf->isFinal()) {
                    $query = static::buildVariantsQuery(static::getVariants((string) $leaf));
                    $result = $dataWorker->sqliteFetchArray($query);

                    foreach ($result as $variant) {
                        $graph->addWordFor($leaf, $variant['word']);

                    }
                    $leaf->setIsFinal();
                }
            }

            if ($mode == 1) {
                if (in_array($elephant, $graph->getWords())) {
                    (var_dump('Найдено'));
                    break;
                }
            } else {
            	if ($wordCount == count($graph->getWords())) {
            		(var_dump('Все слова, найденные в словаре, будут участвовать в подборе'));
                    break;
            	}
            }

            $iterations++;
            echo 'Итераций: ' . $iterations . ' ';
            echo 'Слов найдено: ' . (count($graph->getWords())) . "\n";
            $wordCount = count($graph->getWords());
            // if ($iterations > 20) {
            //     die(var_dump('expression'));
            // }
        }
        var_dump('==========================================');
        //Начало алгоритма Ли
        $end = $graph->getLeafWithValue($elephant);

        $waveLeavesForNextIteration[] = $begin;
        $waveIndex = 0;
        $begin->setWaveIndex($waveIndex);

        (var_dump((string) $end));

        echo "Начало трассировки\n";

        $bContinue = true;

        while ($bContinue) {
            $waveIndex++;
            $currentInterationLeaves = $waveLeavesForNextIteration;
            $waveLeavesForNextIteration = [];
            foreach ($currentInterationLeaves as $key => $leaf) {
                $waveLeavesForNextIteration = array_merge($waveLeavesForNextIteration, $leaf->getLeavesWithoutWaveIndex());
                $leaf->setWaveIndexForChildren($waveIndex);
                if ($leaf->hasLeafWithValue((string) $end) && $end->getWaveIndex()) {
                    echo 'Трассировка закончена ' . $end->getWaveIndex() . "\n";
                    $bContinue = false;
                    break;
                }
            }
        }

        $leafForNextIteration = $end;
        $waveIndex = $end->getWaveIndex() - 1;

        $trace = [(string) $end];

        while (true) {
            if ($waveIndex < 0) {
                break;
            }

            $currentInterationLeaf = $leafForNextIteration;
            foreach ($currentInterationLeaf->getLeaves() as $leaf) {
                if ($leaf->getWaveIndex() == $waveIndex) {
                    $leafForNextIteration = $leaf;
                    $trace[] = (string) $leaf;
                    $waveIndex--;
                    break;
                }
            }
        }

        return array_reverse($trace);
    }

    public static function getVariants($word)
    {
        $variants = [];
        for ($i = 0; $i < mb_strlen($word); $i++) {
            $pattern = '/^([а-я]{' . $i . '})([а-я]{1})(.*)/ui';
            $variants[] = preg_replace($pattern, '$1_$3', $word);
        }

        return $variants;
    }

    public static function buildVariantsQuery($variants)
    {
        $query = 'SELECT word FROM WORDS where ';
        $conditions = [];
        foreach ($variants as $variant) {
            $conditions[] = 'word like "' . $variant . '"';
        }

        return $query . implode(' OR ', $conditions);
    }

}
