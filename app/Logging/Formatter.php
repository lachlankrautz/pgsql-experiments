<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

class Formatter
{
    private const SIMPLE_FORMAT = "%level_name%: %message%\n";

    /**
     * Customize the given logger instance.
     *
     * @param Logger $logger
     * @return void
     */
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            // $handler->setFormatter(new LineFormatter(self::SIMPLE_FORMAT));
        }
    }
}
