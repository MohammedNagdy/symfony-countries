<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240910122356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency ADD CONSTRAINT FK_6956883FF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6956883F77153098 ON currency (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency DROP FOREIGN KEY FK_6956883FF92F3E70');
        $this->addSql('DROP INDEX UNIQ_6956883F77153098 ON currency');
    }
}
