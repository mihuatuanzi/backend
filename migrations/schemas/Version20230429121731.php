<?php

declare(strict_types=1);

namespace DoctrineMigrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230429121731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE authentication (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, credential_type SMALLINT NOT NULL, credential_key VARCHAR(128) NOT NULL, annotation LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FEB4C9FDA76ED395 (user_id), UNIQUE INDEX UNIQ_credential_type_key (credential_type, credential_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dumpling (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, detail LONGTEXT DEFAULT NULL, status SMALLINT NOT NULL, tag VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_DC357829A76ED395 (user_id), INDEX IDX_DC3578292B36786B (title), INDEX IDX_DC357829518597B1 (subtitle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dumpling_applicant (id INT AUTO_INCREMENT NOT NULL, dumpling_id INT NOT NULL, user_id INT NOT NULL, status SMALLINT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_DE4CCCFBE0888F5E (dumpling_id), INDEX IDX_DE4CCCFBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dumpling_member (id INT AUTO_INCREMENT NOT NULL, dumpling_id INT NOT NULL, user_id INT NOT NULL, nickname VARCHAR(64) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', status_mask BIGINT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_31C0DFCFE0888F5E (dumpling_id), INDEX IDX_31C0DFCFA76ED395 (user_id), UNIQUE INDEX UNIQ_31C0DFCFE0888F5EA76ED395 (dumpling_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dumpling_requirement (id INT AUTO_INCREMENT NOT NULL, form_id INT NOT NULL, dumpling_id INT NOT NULL, form_validator_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_38124E205FF69B7D (form_id), INDEX IDX_38124E20E0888F5E (dumpling_id), INDEX IDX_38124E20DA23018E (form_validator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, detail LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_5288FD4FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_field (id INT AUTO_INCREMENT NOT NULL, form_id INT NOT NULL, label VARCHAR(255) NOT NULL, detail LONGTEXT DEFAULT NULL, type SMALLINT NOT NULL, order_number INT NOT NULL, annotation LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_D8B2E19B5FF69B7D (form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_field_validator (id INT AUTO_INCREMENT NOT NULL, form_validator_id INT NOT NULL, form_field_id INT NOT NULL, type SMALLINT NOT NULL, rule VARCHAR(255) DEFAULT NULL, order_number SMALLINT NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_AFC74B9BDA23018E (form_validator_id), INDEX IDX_AFC74B9BF50D82F4 (form_field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_field_value (id INT AUTO_INCREMENT NOT NULL, submission_id INT NOT NULL, field_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_C1BB5EDEE1FD4933 (submission_id), INDEX IDX_C1BB5EDE443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_submission (id INT AUTO_INCREMENT NOT NULL, form_id INT NOT NULL, user_id INT NOT NULL, remarks LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_D2C216675FF69B7D (form_id), INDEX IDX_D2C21667A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_validator (id INT AUTO_INCREMENT NOT NULL, form_id INT DEFAULT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, remarks LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_8BA22FC45FF69B7D (form_id), INDEX IDX_8BA22FC4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, unique_id VARCHAR(64) NOT NULL, type SMALLINT NOT NULL, password VARCHAR(128) DEFAULT NULL, nickname VARCHAR(64) NOT NULL, gender SMALLINT NOT NULL, avatar VARCHAR(255) DEFAULT NULL, signature VARCHAR(255) NOT NULL, exp INT NOT NULL, status SMALLINT NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E3C68343 (unique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_state (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, app_version VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_415129A3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE authentication ADD CONSTRAINT FK_FEB4C9FDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dumpling ADD CONSTRAINT FK_DC357829A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dumpling_applicant ADD CONSTRAINT FK_DE4CCCFBE0888F5E FOREIGN KEY (dumpling_id) REFERENCES dumpling (id)');
        $this->addSql('ALTER TABLE dumpling_applicant ADD CONSTRAINT FK_DE4CCCFBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dumpling_member ADD CONSTRAINT FK_31C0DFCFE0888F5E FOREIGN KEY (dumpling_id) REFERENCES dumpling (id)');
        $this->addSql('ALTER TABLE dumpling_member ADD CONSTRAINT FK_31C0DFCFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dumpling_requirement ADD CONSTRAINT FK_38124E205FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE dumpling_requirement ADD CONSTRAINT FK_38124E20E0888F5E FOREIGN KEY (dumpling_id) REFERENCES dumpling (id)');
        $this->addSql('ALTER TABLE dumpling_requirement ADD CONSTRAINT FK_38124E20DA23018E FOREIGN KEY (form_validator_id) REFERENCES form_validator (id)');
        $this->addSql('ALTER TABLE form ADD CONSTRAINT FK_5288FD4FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE form_field ADD CONSTRAINT FK_D8B2E19B5FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE form_field_validator ADD CONSTRAINT FK_AFC74B9BDA23018E FOREIGN KEY (form_validator_id) REFERENCES form_validator (id)');
        $this->addSql('ALTER TABLE form_field_validator ADD CONSTRAINT FK_AFC74B9BF50D82F4 FOREIGN KEY (form_field_id) REFERENCES form_field (id)');
        $this->addSql('ALTER TABLE form_field_value ADD CONSTRAINT FK_C1BB5EDEE1FD4933 FOREIGN KEY (submission_id) REFERENCES form_submission (id)');
        $this->addSql('ALTER TABLE form_field_value ADD CONSTRAINT FK_C1BB5EDE443707B0 FOREIGN KEY (field_id) REFERENCES form_field (id)');
        $this->addSql('ALTER TABLE form_submission ADD CONSTRAINT FK_D2C216675FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE form_submission ADD CONSTRAINT FK_D2C21667A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE form_validator ADD CONSTRAINT FK_8BA22FC45FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE form_validator ADD CONSTRAINT FK_8BA22FC4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_state ADD CONSTRAINT FK_415129A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE authentication DROP FOREIGN KEY FK_FEB4C9FDA76ED395');
        $this->addSql('ALTER TABLE dumpling DROP FOREIGN KEY FK_DC357829A76ED395');
        $this->addSql('ALTER TABLE dumpling_applicant DROP FOREIGN KEY FK_DE4CCCFBE0888F5E');
        $this->addSql('ALTER TABLE dumpling_applicant DROP FOREIGN KEY FK_DE4CCCFBA76ED395');
        $this->addSql('ALTER TABLE dumpling_member DROP FOREIGN KEY FK_31C0DFCFE0888F5E');
        $this->addSql('ALTER TABLE dumpling_member DROP FOREIGN KEY FK_31C0DFCFA76ED395');
        $this->addSql('ALTER TABLE dumpling_requirement DROP FOREIGN KEY FK_38124E205FF69B7D');
        $this->addSql('ALTER TABLE dumpling_requirement DROP FOREIGN KEY FK_38124E20E0888F5E');
        $this->addSql('ALTER TABLE dumpling_requirement DROP FOREIGN KEY FK_38124E20DA23018E');
        $this->addSql('ALTER TABLE form DROP FOREIGN KEY FK_5288FD4FA76ED395');
        $this->addSql('ALTER TABLE form_field DROP FOREIGN KEY FK_D8B2E19B5FF69B7D');
        $this->addSql('ALTER TABLE form_field_validator DROP FOREIGN KEY FK_AFC74B9BDA23018E');
        $this->addSql('ALTER TABLE form_field_validator DROP FOREIGN KEY FK_AFC74B9BF50D82F4');
        $this->addSql('ALTER TABLE form_field_value DROP FOREIGN KEY FK_C1BB5EDEE1FD4933');
        $this->addSql('ALTER TABLE form_field_value DROP FOREIGN KEY FK_C1BB5EDE443707B0');
        $this->addSql('ALTER TABLE form_submission DROP FOREIGN KEY FK_D2C216675FF69B7D');
        $this->addSql('ALTER TABLE form_submission DROP FOREIGN KEY FK_D2C21667A76ED395');
        $this->addSql('ALTER TABLE form_validator DROP FOREIGN KEY FK_8BA22FC45FF69B7D');
        $this->addSql('ALTER TABLE form_validator DROP FOREIGN KEY FK_8BA22FC4A76ED395');
        $this->addSql('ALTER TABLE user_state DROP FOREIGN KEY FK_415129A3A76ED395');
        $this->addSql('DROP TABLE authentication');
        $this->addSql('DROP TABLE dumpling');
        $this->addSql('DROP TABLE dumpling_applicant');
        $this->addSql('DROP TABLE dumpling_member');
        $this->addSql('DROP TABLE dumpling_requirement');
        $this->addSql('DROP TABLE form');
        $this->addSql('DROP TABLE form_field');
        $this->addSql('DROP TABLE form_field_validator');
        $this->addSql('DROP TABLE form_field_value');
        $this->addSql('DROP TABLE form_submission');
        $this->addSql('DROP TABLE form_validator');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_state');
    }
}
