<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231024124405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_1483a5e95e237e06');
        $this->addSql('ALTER TABLE users ADD first_name VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD last_name VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE users ADD name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE users DROP first_name');
        $this->addSql('ALTER TABLE users DROP last_name');
        $this->addSql('CREATE UNIQUE INDEX uniq_1483a5e95e237e06 ON users (name)');
    }
}
