<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240925180408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist (id VARCHAR(255) NOT NULL, spotify_url VARCHAR(255) NOT NULL, followers_total INT NOT NULL, genres JSON NOT NULL, href VARCHAR(255) NOT NULL, images JSON NOT NULL, name VARCHAR(255) NOT NULL, popularity INT NOT NULL, type VARCHAR(255) NOT NULL, uri VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE track (id VARCHAR(255) NOT NULL, disc_number INT NOT NULL, duration_ms INT NOT NULL, explicit TINYINT(1) DEFAULT NULL, isrc VARCHAR(255) NOT NULL, spotify_url VARCHAR(255) NOT NULL, href VARCHAR(255) NOT NULL, is_local TINYINT(1) DEFAULT NULL, name VARCHAR(255) NOT NULL, popularity INT NOT NULL, preview_url VARCHAR(255) DEFAULT NULL, track_number INT NOT NULL, type VARCHAR(255) NOT NULL, uri VARCHAR(255) NOT NULL, picture_link VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE track');
    }
}
