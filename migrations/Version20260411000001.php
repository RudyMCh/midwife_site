<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260411000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create article table for blog (F2)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article (
            id INT AUTO_INCREMENT NOT NULL,
            featured_image_id INT DEFAULT NULL,
            author_id INT DEFAULT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(128) NOT NULL,
            content LONGTEXT NOT NULL,
            excerpt LONGTEXT DEFAULT NULL,
            is_published TINYINT(1) NOT NULL,
            published_at DATETIME DEFAULT NULL,
            meta_title VARCHAR(255) DEFAULT NULL,
            meta_description LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            deleted_at DATETIME DEFAULT NULL,
            UNIQUE INDEX UNIQ_23A0E66989D9B62 (slug),
            INDEX IDX_23A0E66F8512DC5 (featured_image_id),
            INDEX IDX_23A0E66F675F31B (author_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE article
            ADD CONSTRAINT FK_23A0E66F8512DC5 FOREIGN KEY (featured_image_id) REFERENCES file (id) ON DELETE SET NULL,
            ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES midwife (id) ON DELETE SET NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F8512DC5');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('DROP TABLE article');
    }
}
