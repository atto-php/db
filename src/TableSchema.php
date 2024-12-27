<?php

declare(strict_types=1);

namespace Atto\Db;

use Doctrine\DBAL\Schema\Schema;

interface TableSchema
{
    public function __invoke(Schema $schema);
}