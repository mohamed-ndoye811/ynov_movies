<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206140447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE film_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE movie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE movie (id INT NOT NULL, nom VARCHAR(128) NOT NULL, description TEXT NOT NULL, date_de_parution TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, rate INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE movie_category (movie_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(movie_id, category_id))');
        $this->addSql('CREATE INDEX IDX_DABA824C8F93B6FC ON movie_category (movie_id)');
        $this->addSql('CREATE INDEX IDX_DABA824C12469DE2 ON movie_category (category_id)');
        $this->addSql('ALTER TABLE movie_category ADD CONSTRAINT FK_DABA824C8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE movie_category ADD CONSTRAINT FK_DABA824C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE film_category DROP CONSTRAINT fk_a4cbd6a8567f5183');
        $this->addSql('ALTER TABLE film_category DROP CONSTRAINT fk_a4cbd6a812469de2');
        $this->addSql('DROP TABLE film');
        $this->addSql('DROP TABLE film_category');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE movie_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE film_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE film (id INT NOT NULL, nom VARCHAR(128) NOT NULL, description TEXT NOT NULL, date_de_parution TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, note INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE film_category (film_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(film_id, category_id))');
        $this->addSql('CREATE INDEX idx_a4cbd6a812469de2 ON film_category (category_id)');
        $this->addSql('CREATE INDEX idx_a4cbd6a8567f5183 ON film_category (film_id)');
        $this->addSql('ALTER TABLE film_category ADD CONSTRAINT fk_a4cbd6a8567f5183 FOREIGN KEY (film_id) REFERENCES film (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE film_category ADD CONSTRAINT fk_a4cbd6a812469de2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE movie_category DROP CONSTRAINT FK_DABA824C8F93B6FC');
        $this->addSql('ALTER TABLE movie_category DROP CONSTRAINT FK_DABA824C12469DE2');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE movie_category');
    }
}
