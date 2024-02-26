<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226153615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE movie_category');
        $this->addSql('ALTER TABLE movie DROP CONSTRAINT movie_pkey');
        $this->addSql('ALTER TABLE movie RENAME COLUMN id TO uid');
        $this->addSql('ALTER TABLE movie ADD PRIMARY KEY (uid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE category (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN category.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE movie_category (movie_id UUID NOT NULL, category_id UUID NOT NULL, PRIMARY KEY(movie_id, category_id))');
        $this->addSql('COMMENT ON COLUMN movie_category.movie_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN movie_category.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('DROP INDEX movie_pkey');
        $this->addSql('ALTER TABLE movie RENAME COLUMN uid TO id');
        $this->addSql('ALTER TABLE movie ADD PRIMARY KEY (id)');
    }
}
