<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260412233252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE monthly_amount monthly_amount NUMERIC(10, 2) NOT NULL, CHANGE maintenance_hours_used maintenance_hours_used NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE devis CHANGE total_ht total_ht NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE devis_item CHANGE price price NUMERIC(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE monthly_amount monthly_amount DOUBLE PRECISION NOT NULL, CHANGE maintenance_hours_used maintenance_hours_used DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE devis CHANGE total_ht total_ht DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE devis_item CHANGE price price DOUBLE PRECISION NOT NULL');
    }
}
