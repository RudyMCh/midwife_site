<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260412130015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY `FK_23A0E66F675F31B`');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY `FK_23A0E66F8512DC5`');
        $this->addSql('ALTER TABLE article CHANGE slug slug VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES midwife (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E663569D950 FOREIGN KEY (featured_image_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_23a0e66f8512dc5 TO IDX_23A0E663569D950');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E663569D950');
        $this->addSql('ALTER TABLE article CHANGE slug slug VARCHAR(128) NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT `FK_23A0E66F675F31B` FOREIGN KEY (author_id) REFERENCES midwife (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT `FK_23A0E66F8512DC5` FOREIGN KEY (featured_image_id) REFERENCES file (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_23a0e663569d950 TO IDX_23A0E66F8512DC5');
    }
}
