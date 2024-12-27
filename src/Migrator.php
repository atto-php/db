<?php

declare(strict_types=1);

namespace Atto\Db;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;

final class Migrator
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(
        private Connection $connection,
        private array $migrations
    ) {
    }

    public function migrate(): void
    {
        $schemaDiff = $this->getSchemaDiff();

        $this->getSchemaManager()->alterSchema($schemaDiff);
    }

    public function migrateToString(): string
    {
        $schemaDiff = $this->getSchemaDiff();

        $sql = $this->connection->getDatabasePlatform()->getAlterSchemaSQL($schemaDiff);
        return implode(";\n", $sql);
    }

    private function getSchemaDiff(): SchemaDiff
    {
        $manager = $this->getSchemaManager();

        $schema = new Schema();

        foreach ($this->migrations as $migration) {
            (new $migration)($schema);
        }

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
}