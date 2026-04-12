<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260408160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'SEO: ajout meta_title à Midwife et meta_title/meta_description à InformationPage';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE midwife ADD meta_title VARCHAR(70) DEFAULT NULL');
        $this->addSql('ALTER TABLE midwife MODIFY meta_description VARCHAR(160) DEFAULT NULL');
        $this->addSql('ALTER TABLE information_page ADD meta_title VARCHAR(70) DEFAULT NULL');
        $this->addSql('ALTER TABLE information_page ADD meta_description VARCHAR(160) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE midwife DROP COLUMN meta_title');
        $this->addSql('ALTER TABLE midwife MODIFY meta_description VARCHAR(120) DEFAULT NULL');
        $this->addSql('ALTER TABLE information_page DROP COLUMN meta_title');
        $this->addSql('ALTER TABLE information_page DROP COLUMN meta_description');
    }
}
