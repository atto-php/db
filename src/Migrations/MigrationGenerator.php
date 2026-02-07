<?php

declare(strict_types=1);

namespace Atto\Db;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;

final class MigrationGenerator
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(
        private Connection $connection,
        private array $migrations
    ) {
    }

    /** @return string[] */
    public function generateStatements(): array
    {
        $schemaDiff = $this->getSchemaDiff();

        return $this->connection->getDatabasePlatform()->getAlterSchemaSQL($schemaDiff);
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

    private function getSchemaDiff(): SchemaDiff
    {
        $manager = $this->getSchemaManager();

        $schema = $this->createSchema();

        $schemaDiff = $manager->createComparator()
            ->compareSchemas($manager->introspectSchema(), $schema);
        return $schemaDiff;
    }

    private function getSchemaManager(): AbstractSchemaManager
    {
        if (!isset($this->schemaManager)) {
            $this->schemaManager = $this->connection->createSchemaManager();
        }

        return $this->schemaManager;
    }

    private function createSchema(): Schema
    {
        $schema = new Schema();

        foreach ($this->migrations as $migration) {
            (new $migration())($schema);
        }

        return $schema;
    }

}
