<?php

declare(strict_types=1);

namespace Memcrab\Connector;

use Swoole\Timer;

class Connector
{
    private static \Monolog\Logger $ErrorHandler;

    public function __construct()
    {
    }

    public static function setErrorHandler(\Monolog\Logger $ErrorHandler): void
    {
        self::$ErrorHandler = $ErrorHandler;
    }

    public static function requiredConnection(string $serviceName, callable $connectionMethod, callable $connectionCheckMethod, int $retryTimeout = 1): void
    {
        $connected = false;
        do {
            try {
                if (@$connectionMethod() === false) {
                    throw new \Exception('Connection check failed.');
                }

                if (@$connectionCheckMethod() === false) {
                    throw new \Exception('Connection check failed.');
                }

                self::$ErrorHandler->info($serviceName . ': Connection is running');
                $connected = true;
            } catch (\Exception $e) {
                self::$ErrorHandler->error($serviceName . ': ' . $e->getMessage());
                $connected = false;
                sleep($retryTimeout);
            }
        } while ($connected === false);
    }

    public static function monitor(int $timerId, string $serviceName, callable $connectionMethod, callable $connectionCheckMethod)
    {
        if (@$connectionCheckMethod() === false) {
            self::$ErrorHandler->error($serviceName . ': Connection check failed.');
            if (@$connectionMethod() === false) {
                self::$ErrorHandler->error($serviceName . ': Connection attept failed.');
            } else self::$ErrorHandler->error($serviceName . ': Connection restored.');
        }
    }
}
