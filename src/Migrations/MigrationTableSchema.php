<?php

namespace Atto\Db\Migrations;

use Atto\Db\TableSchema;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;

class MigrationTableSchema implements TableSchema
{
    public function __invoke(Schema $schema): void
    {
        $table = Table::editor()
            ->setUnquotedName('__Migrations')
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('id')
                    ->setTypeName('integer')
                    ->setAutoincrement(true)
                    ->create()
            )
            ->addColumn(
                Column::editor()
                ->setUnquotedName('name')
                ->setTypeName('string')
                ->setLength(255)
                ->create()
            )
            ->addPrimaryKeyConstraint(
                PrimaryKeyConstraint::editor()
                    ->setUnquotedColumnNames('id')
                    ->create()
            )
            ->create();

        ((fn () => $this->_addTable($table))->bindTo($schema, $schema))();
    }
}
