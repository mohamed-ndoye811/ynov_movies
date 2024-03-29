<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329150802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE TABLE movie (uid UUID NOT NULL, name VARCHAR(128) NOT NULL, description TEXT NOT NULL, rate INT NOT NULL, duration INT NOT NULL, PRIMARY KEY(uid))');
        $this->addSql('COMMENT ON COLUMN movie.uid IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
        $this->addSql('CREATE TABLE reservation (uid UUID NOT NULL, rank INT NOT NULL, status VARCHAR(16) NOT NULL, seats INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, movie_uid VARCHAR(36) NOT NULL, PRIMARY KEY(uid))');
        $this->addSql('COMMENT ON COLUMN reservation.uid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reservation.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reservation.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE cinema (uid UUID NOT NULL, name VARCHAR(128) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(uid))');
        $this->addSql('COMMENT ON COLUMN cinema.uid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cinema.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cinema.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE room (uid UUID NOT NULL, cinema UUID DEFAULT NULL, name VARCHAR(128) NOT NULL, seats INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(uid))');
        $this->addSql('CREATE INDEX idx_729f519bd48304b4 ON room (cinema)');
        $this->addSql('COMMENT ON COLUMN room.uid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room.cinema IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN room.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE sceance (uid UUID NOT NULL, room UUID DEFAULT NULL, movie UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(uid))');
        $this->addSql('CREATE INDEX idx_2d854bfe729f519b ON sceance (room)');
        $this->addSql('COMMENT ON COLUMN sceance.uid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sceance.room IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sceance.movie IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sceance.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN sceance.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT fk_729f519bd48304b4 FOREIGN KEY (cinema) REFERENCES cinema (uid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sceance ADD CONSTRAINT fk_2d854bfe729f519b FOREIGN KEY (room) REFERENCES room (uid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE movie');
    }
}
