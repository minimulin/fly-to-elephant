<?php

namespace FlyToElephant\Loggers;

/**
 * Класс вывода сообщений в консоль
 */
class Echoer implements LoggerInterface
{
	public function log($message)
	{
		echo $message . "\n";
	}
}