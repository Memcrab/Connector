<?php

declare(strict_types=1);

namespace Memcrab\Connector;

use function Amp\call;

class Connector
{
    public function __construct()
    {
    }

    public static function requiredConnection(string $serviceName, callable $connectionMethod, callable $connectionCheckMethod, callable $infoLogCallback, int $retryTimeout = 1)
    {
        $connected = false;
        do {
            try {
                $connectionMethod();

                if ($connectionCheckMethod() === true) {
                    $connected = true;
                    $infoLogCallback($serviceName . ': Connection is running');
                } else {
                    throw new \Exception('Connection check failed.');
                }
            } catch (\exception $e) {
                error_log($serviceName . ': ' . $e->getMessage());
                sleep($retryTimeout);
            }
        } while ($connected === false);
    }
}
