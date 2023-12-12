<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231209114950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE opening_hours_restaurant ALTER day_of_week_to TYPE VARCHAR(5)');
        $this->addSql('ALTER TABLE opening_hours_restaurant ALTER day_of_week_to SET NOT NULL');
        $this->addSql('ALTER TABLE opening_hours_restaurant RENAME COLUMN day_of_week TO day_of_week_from');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE opening_hours_restaurant ALTER day_of_week_to TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE opening_hours_restaurant ALTER day_of_week_to DROP NOT NULL');
        $this->addSql('ALTER TABLE opening_hours_restaurant ALTER day_of_week_to TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE opening_hours_restaurant RENAME COLUMN day_of_week_from TO day_of_week');
    }
}
