<?php

namespace Atto\Db\Migrations\Template;

class Migration
{
    private const CODE = <<<'EOF'
        use Doctrine\DBAL\Connection;
        
        return new class() 
        {
            public function getName(): string { return '%1$s'; }
            public function __invoke(Connection $connection): void
            {
                %2$s
            }
        }
    EOF;

    public function __construct(
        private string $name,
        private array $statements,
    ) {
    }

    public function __toString(): string
    {
        foreach ($this->statements as $statement) {
            $statements[] = sprintf("\$connection->executeStatement('%s');\n", $statement);
        }
        return sprintf(self::CODE, $this->name, implode('', $statements));
    }
}