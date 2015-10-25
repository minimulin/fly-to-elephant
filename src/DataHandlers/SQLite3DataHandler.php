<?php

namespace FlyToElephant\DataHandlers;

use SQLite3;

/**
 * Класс работы с SQLite базой данных
 */
class SQLite3DataHandler implements SQLDataHandler
{
    protected static $_instance;
    protected $handler;

    private function __construct($databaseLocation)
    {
        $this->handler = new SQLite3($databaseLocation);
    }

    /**
     * Возвращает массив записей по запросу
     * @param  string $query Строка запроса
     * @return array         Записи
     */
    public function fetchArray($query)
    {
        $query = $this->handler->query($query);
        $array = [];

        while ($result = $query->fetchArray()) {
            $array[] = $result;
        }

        return $array;
    }

    /**
     * Возвращает массив значений колонки из массива записей по запросу
     * @param  string $query  Строка запроса
     * @param  string $column Название колонки
     * @return array          Массив значений
     */
    public function fetchArrayColumn($query, $column)
    {
        $result = [];
        foreach ($this->fetchArray($query) as $record) {
            $result[] = $record[$column];
        }

        return $result;
    }

    /**
     * Возвращает экземпляр класса
     * @param  string $databaseLocation Расположение файла БД
     * @return SQLite3DataHandler       Экземпляр класса
     */
    public static function getInstance($databaseLocation)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($databaseLocation);
        }
        return self::$_instance;
    }

}
