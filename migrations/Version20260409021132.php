<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260409021132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devis (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(50) NOT NULL, client_first_name VARCHAR(100) NOT NULL, client_last_name VARCHAR(100) NOT NULL, client_email VARCHAR(255) NOT NULL, client_phone VARCHAR(20) DEFAULT NULL, client_company VARCHAR(255) DEFAULT NULL, client_address VARCHAR(255) DEFAULT NULL, client_siret VARCHAR(50) DEFAULT NULL, total_ht DOUBLE PRECISION NOT NULL, tva_rate DOUBLE PRECISION NOT NULL, total_ttc DOUBLE PRECISION NOT NULL, validity_days INT NOT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8B27C52BAEA34913 (reference), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE devis_item (id INT AUTO_INCREMENT NOT NULL, category_name VARCHAR(255) NOT NULL, item_name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, is_monthly TINYINT NOT NULL, devis_id INT NOT NULL, INDEX IDX_50C944C141DEFADA (devis_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE devis_item ADD CONSTRAINT FK_50C944C141DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_item DROP FOREIGN KEY FK_50C944C141DEFADA');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE devis_item');
    }
}
