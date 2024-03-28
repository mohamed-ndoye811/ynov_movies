<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240328205436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sceance ADD room UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN sceance.room IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE sceance ADD CONSTRAINT FK_2D854BFE729F519B FOREIGN KEY (room) REFERENCES room (uid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2D854BFE729F519B ON sceance (room)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE sceance DROP CONSTRAINT FK_2D854BFE729F519B');
        $this->addSql('DROP INDEX IDX_2D854BFE729F519B');
        $this->addSql('ALTER TABLE sceance DROP room');
    }
}
