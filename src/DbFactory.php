<?php

declare(strict_types=1);

namespace Atto\Db;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

final class DbFactory
{
    public function __invoke(array $config): Connection
    {
        return DriverManager::getConnection($config);
    }
}