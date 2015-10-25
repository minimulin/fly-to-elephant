<?php

namespace FlyToElephant\DataHandlers;

use SQLite3;

/**
 *
 */
class SQLite3DataHandler
{
    protected static $_instance;
    protected $handler;

    private function __construct($databaseLocation)
    {
        $this->handler = new SQLite3($databaseLocation);
    }

    public function sqliteFetchArray($query)
    {
        $query = $this->handler->query($query);
        $array = [];

        while ($result = $query->fetchArray()) {
            $array[] = $result;
        }

        return $array;
    }

    public static function getInstance($databaseLocation)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($databaseLocation);
        }
        return self::$_instance;
    }

}
