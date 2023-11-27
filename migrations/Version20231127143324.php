<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127143324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE film_category (film_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(film_id, category_id))');
        $this->addSql('CREATE INDEX IDX_A4CBD6A8567F5183 ON film_category (film_id)');
        $this->addSql('CREATE INDEX IDX_A4CBD6A812469DE2 ON film_category (category_id)');
        $this->addSql('ALTER TABLE film_category ADD CONSTRAINT FK_A4CBD6A8567F5183 FOREIGN KEY (film_id) REFERENCES film (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE film_category ADD CONSTRAINT FK_A4CBD6A812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('ALTER TABLE film_category DROP CONSTRAINT FK_A4CBD6A8567F5183');
        $this->addSql('ALTER TABLE film_category DROP CONSTRAINT FK_A4CBD6A812469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE film_category');
    }
}
