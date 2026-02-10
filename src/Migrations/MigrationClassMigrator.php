<?php

namespace Atto\Db\Migrations;

use DirectoryIterator;
use Doctrine\DBAL\Connection;

class MigrationClassMigrator implements Migrator
{
    private array $migrations = [];

    public function __construct(
        private Connection $connection,
        private string $migrationsDirectory,
    ) {
    }

    public function migrate(): void
    {
        $this->loadExistingMigrations();

        $directory = new DirectoryIterator($this->migrationsDirectory);
        $migrations = [];

        foreach ($directory as $file) {
            if ($file->isDot()) {
                continue;
            }

            $migration = include $file->getPathname();
            $name = $migration->getName();

            $migrations[$name] = $migration;
        }

        ksort($migrations);

        foreach ($migrations as $name => $migration) {
            printf("Migrating %s\n", $name);

            if (!$this->hasMigrated($name)) {
                $migration($this->connection);
                $this->connection->insert('__Migrations', ['name' => $name]);
                $this->migrations[$name] = $name;
            }
        }
    }

    private function hasMigrated(string $name): bool
    {
        return isset($this->migrations[$name]);
    }

    private function loadExistingMigrations(): void
    {
        $exists = $this->connection->executeQuery('SHOW TABLES LIKE "\_\_Migrations"')->rowCount() > 0;

        if (!$exists) {
            return;
        }

        $migrations = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('__Migrations')
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($migrations as $migration) {
            $this->migrations[$migration['name']] = $migration['name'];
        }
    }
}