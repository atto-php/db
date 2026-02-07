<?php

namespace Atto\Db\Migrations;

use Atto\CodegenTools\ClassDefinition\PHPClassDefinitionProducer;
use Atto\CodegenTools\ClassDefinition\SimplePHPClassDefinition;
use Atto\CodegenTools\CodeGeneration\PHPFilesWriter;
use Atto\Db\Migrations\Template\Migration;

class MigrationClassWriter
{
    public function __construct(
        private MigrationGenerator $migrationGenerator,
        private string $outputDirectory,
    ) {
    }

    public function migrateToClass(): void
    {
        $name = (new \DateTime())->format('Y_m_d_His');
        $phpCode = new SimplePHPClassDefinition(
            'Migrations',
            'Migration' . $name,
            (string)(new Migration($name, $this->migrationGenerator->generateStatements()))
        );

        $classWriters = new PHPFilesWriter($this->outputDirectory, 'Migrations');

        $classWriters->writeFiles(new PHPClassDefinitionProducer(
            (fn () => yield $phpCode)()
        ));
    }
}
