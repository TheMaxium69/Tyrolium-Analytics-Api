<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260720121501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE input (id INT AUTO_INCREMENT NOT NULL, ip LONGTEXT NOT NULL, page_name VARCHAR(255) NOT NULL, uri LONGTEXT NOT NULL, is_login TINYINT NOT NULL, tag_id_id INT NOT NULL, INDEX IDX_D82832D75DA88751 (tag_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, tag VARCHAR(255) NOT NULL, domain_names JSON NOT NULL, created_at DATETIME NOT NULL, useritium_id INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE input ADD CONSTRAINT FK_D82832D75DA88751 FOREIGN KEY (tag_id_id) REFERENCES projects (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE input DROP FOREIGN KEY FK_D82832D75DA88751');
        $this->addSql('DROP TABLE input');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
