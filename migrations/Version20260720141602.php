<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260720141602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_D82832D75DA88751 ON input');
        $this->addSql('ALTER TABLE input CHANGE tag_id_id tag_id INT NOT NULL');
        $this->addSql('ALTER TABLE input ADD CONSTRAINT FK_D82832D7BAD26311 FOREIGN KEY (tag_id) REFERENCES projects (id)');
        $this->addSql('CREATE INDEX IDX_D82832D7BAD26311 ON input (tag_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE input DROP FOREIGN KEY FK_D82832D7BAD26311');
        $this->addSql('DROP INDEX IDX_D82832D7BAD26311 ON input');
        $this->addSql('ALTER TABLE input CHANGE tag_id tag_id_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_D82832D75DA88751 ON input (tag_id_id)');
    }
}
