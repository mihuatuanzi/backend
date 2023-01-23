<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230123081737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE authentication (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, credential_type SMALLINT NOT NULL, credential_key VARCHAR(128) NOT NULL, annotation LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FEB4C9FDA76ED395 (user_id), UNIQUE INDEX UNIQ_credential_type_key (credential_type, credential_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, unique_id VARCHAR(64) NOT NULL, password VARCHAR(128) DEFAULT NULL, nickname VARCHAR(64) NOT NULL, gender SMALLINT NOT NULL, avatar VARCHAR(255) DEFAULT NULL, signature VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E3C68343 (unique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE authentication ADD CONSTRAINT FK_FEB4C9FDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE authentication DROP FOREIGN KEY FK_FEB4C9FDA76ED395');
        $this->addSql('DROP TABLE authentication');
        $this->addSql('DROP TABLE user');
    }
}
