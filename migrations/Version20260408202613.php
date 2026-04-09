<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408202613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE domain ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_8C9F3610989D9B62 ON file');
        $this->addSql('ALTER TABLE file DROP slug');
        $this->addSql('ALTER TABLE midwife ADD rpps VARCHAR(11) DEFAULT NULL, ADD adeli VARCHAR(9) DEFAULT NULL, ADD rcp_libelle VARCHAR(255) DEFAULT NULL, ADD rcp_numero_contrat VARCHAR(100) DEFAULT NULL, ADD numero_ordinal VARCHAR(50) DEFAULT NULL, ADD siret VARCHAR(14) DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD deleted_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE domain DROP created_at, DROP updated_at, DROP deleted_at');
        $this->addSql('ALTER TABLE file ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C9F3610989D9B62 ON file (slug)');
        $this->addSql('ALTER TABLE midwife DROP rpps, DROP adeli, DROP rcp_libelle, DROP rcp_numero_contrat, DROP numero_ordinal, DROP siret, DROP created_at, DROP updated_at, DROP deleted_at');
        $this->addSql('ALTER TABLE service DROP deleted_at');
    }
}
