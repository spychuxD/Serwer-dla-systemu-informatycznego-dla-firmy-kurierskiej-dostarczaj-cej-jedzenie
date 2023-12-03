<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231203131008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE payment (id INT NOT NULL, user_id_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, transaction_id VARCHAR(255) NOT NULL, date_creation VARCHAR(255) NOT NULL, date_realization VARCHAR(255) DEFAULT NULL, currency VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D28840D9D86650F ON payment (user_id_id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE payment_id_seq CASCADE');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D9D86650F');
        $this->addSql('DROP TABLE payment');
    }
}
