<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240328191820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room ADD cinema UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN room.cinema IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BD48304B4 FOREIGN KEY (cinema) REFERENCES cinema (uid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_729F519BD48304B4 ON room (cinema)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE room DROP CONSTRAINT FK_729F519BD48304B4');
        $this->addSql('DROP INDEX IDX_729F519BD48304B4');
        $this->addSql('ALTER TABLE room DROP cinema');
    }
}
