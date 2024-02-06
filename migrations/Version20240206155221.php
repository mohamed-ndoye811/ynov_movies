<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206155221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE movie_id_seq CASCADE');
        $this->addSql('ALTER TABLE movie ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN movie.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE movie_category ALTER movie_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN movie_category.movie_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE movie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE movie ALTER id TYPE INT');
        $this->addSql('COMMENT ON COLUMN movie.id IS NULL');
        $this->addSql('ALTER TABLE movie_category ALTER movie_id TYPE INT');
        $this->addSql('COMMENT ON COLUMN movie_category.movie_id IS NULL');
    }
}
