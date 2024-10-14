<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014122616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite ADD artist_id VARCHAR(255) DEFAULT NULL, DROP artist');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('CREATE INDEX IDX_68C58ED9B7970CF8 ON favorite (artist_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9B7970CF8');
        $this->addSql('DROP INDEX IDX_68C58ED9B7970CF8 ON favorite');
        $this->addSql('ALTER TABLE favorite ADD artist VARCHAR(255) NOT NULL, DROP artist_id');
    }
}
