<?php

namespace Atto\Db\Migrations;

interface Migrator
{
    public function migrate(): void;
}