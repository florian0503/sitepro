<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260429200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ville_prospection and prospect tables for prospection feature';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ville_prospection (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, code_postal VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prospect (id INT AUTO_INCREMENT NOT NULL, ville_id INT NOT NULL, nom_boite VARCHAR(255) NOT NULL, adresse VARCHAR(500) NOT NULL, telephone VARCHAR(20) DEFAULT NULL, horaires VARCHAR(255) DEFAULT NULL, statut VARCHAR(50) NOT NULL, site_web_actuel VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, date_contact DATE DEFAULT NULL, position INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C9CE8C7DA73F0036 (ville_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prospect ADD CONSTRAINT FK_C9CE8C7DA73F0036 FOREIGN KEY (ville_id) REFERENCES ville_prospection (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE prospect DROP FOREIGN KEY FK_C9CE8C7DA73F0036');
        $this->addSql('DROP TABLE prospect');
        $this->addSql('DROP TABLE ville_prospection');
    }
}
