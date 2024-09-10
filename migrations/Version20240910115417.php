<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240910115417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, symbol VARCHAR(10) NOT NULL, INDEX IDX_6956883FF92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE currency ADD CONSTRAINT FK_6956883FF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE country DROP currency_name, DROP currency_symbol');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency DROP FOREIGN KEY FK_6956883FF92F3E70');
        $this->addSql('DROP TABLE currency');
        $this->addSql('ALTER TABLE country ADD currency_name VARCHAR(255) NOT NULL, ADD currency_symbol VARCHAR(10) NOT NULL');
    }
}
