<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408142139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Schéma initial : toutes les entités métier, MediaFile (remplace FileManagerBundle)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE degree (id INT AUTO_INCREMENT NOT NULL, establishment VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, year VARCHAR(4) DEFAULT NULL, type VARCHAR(255) NOT NULL, midwife_id INT NOT NULL, INDEX IDX_A7A36D63E64C57C1 (midwife_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE domain (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, title_color_bg VARCHAR(10) DEFAULT NULL, slug VARCHAR(128) NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, title_bg_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A7A91E0B989D9B62 (slug), INDEX IDX_A7A91E0BDAD0B6DF (title_bg_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) NOT NULL, directory VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, alt VARCHAR(255) DEFAULT NULL, is_video TINYINT DEFAULT NULL, is_iframe TINYINT DEFAULT NULL, video_url VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8C9F3610989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE home_page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, catchphrase VARCHAR(500) NOT NULL, about VARCHAR(1000) DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, background_image1_id INT DEFAULT NULL, background_image2_id INT DEFAULT NULL, title_bg_id INT DEFAULT NULL, INDEX IDX_352C07EFE089BD4D (background_image1_id), INDEX IDX_352C07EFF23C12A3 (background_image2_id), INDEX IDX_352C07EFDAD0B6DF (title_bg_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE home_page_file (home_page_id INT NOT NULL, media_file_id INT NOT NULL, INDEX IDX_758F538EB966A8BC (home_page_id), INDEX IDX_758F538EF21CFF25 (media_file_id), PRIMARY KEY (home_page_id, media_file_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE information_page (id INT AUTO_INCREMENT NOT NULL, legal LONGTEXT NOT NULL, coming LONGTEXT DEFAULT NULL, price LONGTEXT DEFAULT NULL, links LONGTEXT DEFAULT NULL, mention VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, title_bg_id INT DEFAULT NULL, INDEX IDX_35A2BF3DDAD0B6DF (title_bg_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE midwife (id INT AUTO_INCREMENT NOT NULL, lastname VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, firstname VARCHAR(255) NOT NULL, about_me VARCHAR(5000) DEFAULT NULL, description VARCHAR(5000) DEFAULT NULL, background_color1 VARCHAR(255) DEFAULT NULL, doctolib_url VARCHAR(255) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(120) DEFAULT NULL, picture_id INT DEFAULT NULL, bg_card_id INT DEFAULT NULL, bg_title_id INT DEFAULT NULL, picture_self_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8BD54C38989D9B62 (slug), INDEX IDX_8BD54C38EE45BDBF (picture_id), INDEX IDX_8BD54C38553CF462 (bg_card_id), INDEX IDX_8BD54C389252576F (bg_title_id), INDEX IDX_8BD54C38DE7C5142 (picture_self_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE midwife_file (midwife_id INT NOT NULL, media_file_id INT NOT NULL, INDEX IDX_C21CE492E64C57C1 (midwife_id), INDEX IDX_C21CE492F21CFF25 (media_file_id), PRIMARY KEY (midwife_id, media_file_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE office (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, zipcode VARCHAR(5) NOT NULL, city VARCHAR(255) NOT NULL, phone VARCHAR(15) DEFAULT NULL, about VARCHAR(2000) DEFAULT NULL, url_google_map VARCHAR(600) DEFAULT NULL, latitude VARCHAR(255) DEFAULT NULL, longitude VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE path (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, start VARCHAR(4) DEFAULT NULL, end VARCHAR(4) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, midwife_id INT NOT NULL, INDEX IDX_B548B0FE64C57C1 (midwife_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, slug VARCHAR(128) NOT NULL, position INT DEFAULT NULL, domain_id INT DEFAULT NULL, picture_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_E19D9AD2989D9B62 (slug), INDEX IDX_E19D9AD2115F0EE5 (domain_id), INDEX IDX_E19D9AD2EE45BDBF (picture_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE service_midwife (service_id INT NOT NULL, midwife_id INT NOT NULL, INDEX IDX_44511EA4ED5CA9E6 (service_id), INDEX IDX_44511EA4E64C57C1 (midwife_id), PRIMARY KEY (service_id, midwife_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE degree ADD CONSTRAINT FK_A7A36D63E64C57C1 FOREIGN KEY (midwife_id) REFERENCES midwife (id)');
        $this->addSql('ALTER TABLE domain ADD CONSTRAINT FK_A7A91E0BDAD0B6DF FOREIGN KEY (title_bg_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE home_page ADD CONSTRAINT FK_352C07EFE089BD4D FOREIGN KEY (background_image1_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE home_page ADD CONSTRAINT FK_352C07EFF23C12A3 FOREIGN KEY (background_image2_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE home_page ADD CONSTRAINT FK_352C07EFDAD0B6DF FOREIGN KEY (title_bg_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE home_page_file ADD CONSTRAINT FK_758F538EB966A8BC FOREIGN KEY (home_page_id) REFERENCES home_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE home_page_file ADD CONSTRAINT FK_758F538EF21CFF25 FOREIGN KEY (media_file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE information_page ADD CONSTRAINT FK_35A2BF3DDAD0B6DF FOREIGN KEY (title_bg_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE midwife ADD CONSTRAINT FK_8BD54C38EE45BDBF FOREIGN KEY (picture_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE midwife ADD CONSTRAINT FK_8BD54C38553CF462 FOREIGN KEY (bg_card_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE midwife ADD CONSTRAINT FK_8BD54C389252576F FOREIGN KEY (bg_title_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE midwife ADD CONSTRAINT FK_8BD54C38DE7C5142 FOREIGN KEY (picture_self_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE midwife_file ADD CONSTRAINT FK_C21CE492E64C57C1 FOREIGN KEY (midwife_id) REFERENCES midwife (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE midwife_file ADD CONSTRAINT FK_C21CE492F21CFF25 FOREIGN KEY (media_file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE path ADD CONSTRAINT FK_B548B0FE64C57C1 FOREIGN KEY (midwife_id) REFERENCES midwife (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2115F0EE5 FOREIGN KEY (domain_id) REFERENCES domain (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2EE45BDBF FOREIGN KEY (picture_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE service_midwife ADD CONSTRAINT FK_44511EA4ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_midwife ADD CONSTRAINT FK_44511EA4E64C57C1 FOREIGN KEY (midwife_id) REFERENCES midwife (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE degree DROP FOREIGN KEY FK_A7A36D63E64C57C1');
        $this->addSql('ALTER TABLE domain DROP FOREIGN KEY FK_A7A91E0BDAD0B6DF');
        $this->addSql('ALTER TABLE home_page DROP FOREIGN KEY FK_352C07EFE089BD4D');
        $this->addSql('ALTER TABLE home_page DROP FOREIGN KEY FK_352C07EFF23C12A3');
        $this->addSql('ALTER TABLE home_page DROP FOREIGN KEY FK_352C07EFDAD0B6DF');
        $this->addSql('ALTER TABLE home_page_file DROP FOREIGN KEY FK_758F538EB966A8BC');
        $this->addSql('ALTER TABLE home_page_file DROP FOREIGN KEY FK_758F538EF21CFF25');
        $this->addSql('ALTER TABLE information_page DROP FOREIGN KEY FK_35A2BF3DDAD0B6DF');
        $this->addSql('ALTER TABLE midwife DROP FOREIGN KEY FK_8BD54C38EE45BDBF');
        $this->addSql('ALTER TABLE midwife DROP FOREIGN KEY FK_8BD54C38553CF462');
        $this->addSql('ALTER TABLE midwife DROP FOREIGN KEY FK_8BD54C389252576F');
        $this->addSql('ALTER TABLE midwife DROP FOREIGN KEY FK_8BD54C38DE7C5142');
        $this->addSql('ALTER TABLE midwife_file DROP FOREIGN KEY FK_C21CE492E64C57C1');
        $this->addSql('ALTER TABLE midwife_file DROP FOREIGN KEY FK_C21CE492F21CFF25');
        $this->addSql('ALTER TABLE path DROP FOREIGN KEY FK_B548B0FE64C57C1');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2115F0EE5');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2EE45BDBF');
        $this->addSql('ALTER TABLE service_midwife DROP FOREIGN KEY FK_44511EA4ED5CA9E6');
        $this->addSql('ALTER TABLE service_midwife DROP FOREIGN KEY FK_44511EA4E64C57C1');
        $this->addSql('DROP TABLE degree');
        $this->addSql('DROP TABLE domain');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE home_page');
        $this->addSql('DROP TABLE home_page_file');
        $this->addSql('DROP TABLE information_page');
        $this->addSql('DROP TABLE midwife');
        $this->addSql('DROP TABLE midwife_file');
        $this->addSql('DROP TABLE office');
        $this->addSql('DROP TABLE path');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_midwife');
        $this->addSql('DROP TABLE `user`');
    }
}
