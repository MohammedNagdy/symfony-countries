<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240910113738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country CHANGE region region VARCHAR(255) DEFAULT NULL, CHANGE sub_region sub_region VARCHAR(255) DEFAULT NULL, CHANGE demonym demonym VARCHAR(255) DEFAULT NULL, CHANGE flag flag VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country CHANGE name name VARCHAR(255) NOT NULL, CHANGE region region VARCHAR(255) NOT NULL, CHANGE sub_region sub_region VARCHAR(255) NOT NULL, CHANGE demonym demonym VARCHAR(255) NOT NULL, CHANGE flag flag VARCHAR(255) NOT NULL');
    }
}
