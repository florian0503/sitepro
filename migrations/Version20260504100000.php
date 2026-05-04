<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260504100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add position field to category for custom ordering';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category ADD position INT NOT NULL DEFAULT 99');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category DROP COLUMN position');
    }
}
