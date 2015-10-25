<?php

namespace FlyToElephant\DataHandlers;

/**
 * Интерфейс работы с SQL базой данных
 */
interface SQLDataHandler
{
    public function fetchArray($query);
    public function fetchArrayColumn($query, $column);
    public static function getInstance($databaseInformation);
}
