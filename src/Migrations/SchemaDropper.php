<?php

namespace Atto\Db\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class SchemaDropper
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(
        private Connection $connection
    ) {
    }

    public function drop(): void
    {
        $schema = $this->getSchemaManager()->introspectSchema();

        $this->getSchemaManager()->dropSchemaObjects($schema);
    }

    public function dropToString(): string
    {
        $queries = $this->getSchemaManager()
            ->introspectSchema()
            ->toDropSql($this->connection->getDatabasePlatform());

        return implode(";\n", $queries);
    }

    private function getSchemaManager(): AbstractSchemaManager
    {
        if (!isset($this->schemaManager)) {
            $this->schemaManager = $this->connection->createSchemaManager();
        }

        return $this->schemaManager;
    }
}