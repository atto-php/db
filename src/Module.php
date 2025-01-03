<?php

declare(strict_types=1);

namespace Atto\Db;

use Atto\Framework\Module\ModuleInterface;
use Doctrine\DBAL\Connection;

final class Module implements ModuleInterface
{
    public function getServices(): array
    {
        return [
            Connection::class => [
                'factory' => new DbFactory(),
                'args' => [
                    'config.database'
                ]
            ],

            Migrator::class => [
                'args' => [
                    Connection::class,
                    'config.schemas'
                ]
            ]
        ];
    }

    public function getConfig(): array
    {
        return [
            'database' => [],
            'schemas' => []
        ];
    }

}