<?php

namespace FlyToElephant;

use FlyToElephant\DataHandlers\SQLite3DataHandler;
use FlyToElephant\Graph\Algorithms\LeeAlgorithm;
use FlyToElephant\Graph\WordGraph;
use FlyToElephant\Loggers\Echoer;

/**
 * Класс превращения мухи в слона
 */
class FlyToElephant
{
    public static function magic($fly, $elephant)
    {
        //Логгер в данном случае выводит сообщения в консоль
        $logger = new Echoer;

        if (mb_strlen($fly) != mb_strlen($elephant)) {
            $logger->log('Слова имеют разную длину');
            return null;
        }

        //@todo: Добавить проверку на существование в словаре $fly и $elephant. Сейчас при их отсутствии решение не находится без дополнительных сообщений

        //Получаем из словаря все слова с заданной длиной
        $words = static::getWordsFromDictionary(mb_strlen($fly));

        //Генерируем граф на основе этих слов
        $graph = new WordGraph($words, $logger);
        $begin = $graph->getLeafWithValue($fly);
        $end = $graph->getLeafWithValue($elephant);

        if ($begin && $end) {
            //Запускаем алгоритм поиска кратчайшего пути
            $chain = LeeAlgorithm::run($graph, $begin, $end, $logger);
            return $chain;
        } else {
            return null;
        }
    }

    public function getWordsFromDictionary($length)
    {
        $dataWorker = SQLite3DataHandler::getInstance('dictionary.sqlite');
        return $dataWorker->fetchArrayColumn('SELECT * FROM words WHERE length(word) = ' . $length, 'word');
    }
}
