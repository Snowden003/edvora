<?php

namespace App\Logging;

use Monolog\Logger;

class TelegramLogger
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        return new Logger(
            config('app.name', 'Laravel'),
            [
                new TelegramHandler($config),
            ]
        );
    }
}
