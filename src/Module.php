<?php

declare(strict_types=1);

namespace Atto\Db;

use Atto\Db\Migrations\DynamicMigrator;
use Atto\Db\Migrations\MigrationClassMigrator;
use Atto\Db\Migrations\MigrationClassWriter;
use Atto\Db\Migrations\MigrationTableSchema;
use Atto\Db\Migrations\SchemaDropper;
use Atto\Framework\Module\ModuleInterface;
use Atto\Db\Migrations\MigrationGenerator;
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

            DynamicMigrator::class => [
                'args' => [
                    Connection::class,
                    MigrationGenerator::class
                ]
            ],
            MigrationClassMigrator::class => [
                'args' => [
                    Connection::class,
                    'config.migrations_directory'
                ]
            ],

            MigrationGenerator::class => [
                'args' => [
                    Connection::class,
                    'config.schemas'
                ]
            ],
            MigrationClassWriter::class => [
                'args' => [
                    MigrationGenerator::class,
                    'config.migrations_directory'
                ]
            ],
            SchemaDropper::class => [
                'args' => [
                    Connection::class,
                ]
            ]
        ];
    }

    public function getConfig(): array
    {
        return [
            'migrations_directory' => './migrations',
            'database' => [],
            'schemas' => [
                MigrationTableSchema::class,
            ]
        ];
    }

}