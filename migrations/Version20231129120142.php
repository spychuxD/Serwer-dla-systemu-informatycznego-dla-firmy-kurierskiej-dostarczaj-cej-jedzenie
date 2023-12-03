<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129120142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE favorite_restaurants_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE favorite_restaurants (id INT NOT NULL, restaurant_id INT NOT NULL, user_data_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6E913267B1E7706E ON favorite_restaurants (restaurant_id)');
        $this->addSql('CREATE INDEX IDX_6E9132676FF8BF36 ON favorite_restaurants (user_data_id)');
        $this->addSql('ALTER TABLE favorite_restaurants ADD CONSTRAINT FK_6E913267B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite_restaurants ADD CONSTRAINT FK_6E9132676FF8BF36 FOREIGN KEY (user_data_id) REFERENCES user_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE favorite_restaurants_id_seq CASCADE');
        $this->addSql('ALTER TABLE favorite_restaurants DROP CONSTRAINT FK_6E913267B1E7706E');
        $this->addSql('ALTER TABLE favorite_restaurants DROP CONSTRAINT FK_6E9132676FF8BF36');
        $this->addSql('DROP TABLE favorite_restaurants');
    }
}
