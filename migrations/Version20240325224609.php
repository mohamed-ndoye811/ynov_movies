<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240325224609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cinema (uid UUID NOT NULL, name VARCHAR(128) NOT NULL, PRIMARY KEY(uid))');
        $this->addSql('COMMENT ON COLUMN cinema.uid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE reservation (uid UUID NOT NULL, rank INT NOT NULL, status VARCHAR(16) NOT NULL, seats INT NOT NULL, PRIMARY KEY(uid))');
        $this->addSql('COMMENT ON COLUMN reservation.uid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE room (uid UUID NOT NULL, name VARCHAR(128) NOT NULL, seats INT NOT NULL, PRIMARY KEY(uid))');
        $this->addSql('COMMENT ON COLUMN room.uid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE sceance (uid UUID NOT NULL, movie UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(uid))');
        $this->addSql('COMMENT ON COLUMN sceance.uid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sceance.movie IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE reservation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE room_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sceance_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE cinema');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE sceance');
    }
}
