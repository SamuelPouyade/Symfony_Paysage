<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018195800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE articles DROP commentaire');
        $this->addSql('ALTER TABLE articles DROP image');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31683DA5256D FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BFDD31683DA5256D ON articles (image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE articles DROP CONSTRAINT FK_BFDD31683DA5256D');
        $this->addSql('DROP INDEX IDX_BFDD31683DA5256D');
        $this->addSql('ALTER TABLE articles ADD commentaire TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE articles ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE articles DROP image_id');
    }
}
