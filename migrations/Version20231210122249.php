<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231210122249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EB95123F7E3C61F9 ON restaurant (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE restaurant DROP CONSTRAINT FK_EB95123F7E3C61F9');
        $this->addSql('DROP INDEX UNIQ_EB95123F7E3C61F9');
        $this->addSql('ALTER TABLE restaurant DROP owner_id');
    }
}
