<?php

declare(strict_types=1);

namespace Memcrab\Connector;

use Memcrab\Log\Log;

class Connector
{
    public function __construct(private Log $Log)
    {
    }

    public function retryConnection(callable $connectionMethod, callable $connectionCheckMethod, callable $info, int $retryTimeout = 1)
    {
        $connected = false;
        do {
            $connectionMethod();
            if ($connectionCheckMethod() === true) {
                $connected = true;
                $info('Connection is running');
            } else {
                error_log("Connection lost");
                sleep($retryTimeout);
            }
        } while ($connected === false);
    }
}
