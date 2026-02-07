<?php

namespace Atto\Db\Migrations;

use Atto\Db\MigrationGenerator;
use Doctrine\DBAL\Connection;

class DynamicMigrator implements Migrator
{
    public function __construct(
        private Connection $connection,
        private MigrationGenerator $migrationGenerator,
    ) {
    }

    public function migrate(): void
    {
        $sql = $this->migrationGenerator->generateStatements();

        foreach ($sql as $statement) {
            $this->connection->executeStatement($statement);
        }
    }
}
