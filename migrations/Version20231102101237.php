<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231102101237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE address_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE dish_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE dish_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE dish_ingridient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ingridient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ingridient_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE opening_hours_restaurant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE restaurant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE restaurant_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_data_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE address (id INT NOT NULL, street VARCHAR(255) NOT NULL, parcel_number VARCHAR(255) DEFAULT NULL, apartment_number VARCHAR(255) DEFAULT NULL, postcode VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE dish (id INT NOT NULL, restaurant_id INT NOT NULL, dish_category_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_957D8CB8B1E7706E ON dish (restaurant_id)');
        $this->addSql('CREATE INDEX IDX_957D8CB8C057AE07 ON dish (dish_category_id)');
        $this->addSql('CREATE TABLE dish_category (id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE dish_ingridient (id INT NOT NULL, dish_id INT NOT NULL, ingridient_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DBA02D148EB0CB ON dish_ingridient (dish_id)');
        $this->addSql('CREATE INDEX IDX_DBA02D750B1398 ON dish_ingridient (ingridient_id)');
        $this->addSql('CREATE TABLE ingridient (id INT NOT NULL, ingridient_category_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1C6DB80BB6CC2A71 ON ingridient (ingridient_category_id)');
        $this->addSql('CREATE TABLE ingridient_category (id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, is_multi_option BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE opening_hours_restaurant (id INT NOT NULL, restaurant_id INT NOT NULL, day_of_week VARCHAR(5) NOT NULL, open_hour TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, close_hour TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DD09684AB1E7706E ON opening_hours_restaurant (restaurant_id)');
        $this->addSql('CREATE TABLE restaurant (id INT NOT NULL, restaurant_address_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, file_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EB95123F508D8F44 ON restaurant (restaurant_address_id)');
        $this->addSql('CREATE TABLE restaurant_category (id INT NOT NULL, restaurant_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_26E9D72EB1E7706E ON restaurant_category (restaurant_id)');
        $this->addSql('CREATE INDEX IDX_26E9D72E12469DE2 ON restaurant_category (category_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE user_data (id INT NOT NULL, id_user_id INT NOT NULL, main_address_id INT NOT NULL, contact_address_id INT DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, date_of_birth TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, phone_number VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D772BFAA79F37AE5 ON user_data (id_user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D772BFAACD4FDB16 ON user_data (main_address_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D772BFAA320EF6E2 ON user_data (contact_address_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB8B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB8C057AE07 FOREIGN KEY (dish_category_id) REFERENCES dish_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dish_ingridient ADD CONSTRAINT FK_DBA02D148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dish_ingridient ADD CONSTRAINT FK_DBA02D750B1398 FOREIGN KEY (ingridient_id) REFERENCES ingridient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ingridient ADD CONSTRAINT FK_1C6DB80BB6CC2A71 FOREIGN KEY (ingridient_category_id) REFERENCES ingridient_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE opening_hours_restaurant ADD CONSTRAINT FK_DD09684AB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123F508D8F44 FOREIGN KEY (restaurant_address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE restaurant_category ADD CONSTRAINT FK_26E9D72EB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE restaurant_category ADD CONSTRAINT FK_26E9D72E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_data ADD CONSTRAINT FK_D772BFAA79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_data ADD CONSTRAINT FK_D772BFAACD4FDB16 FOREIGN KEY (main_address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_data ADD CONSTRAINT FK_D772BFAA320EF6E2 FOREIGN KEY (contact_address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE address_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE dish_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE dish_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE dish_ingridient_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ingridient_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ingridient_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE opening_hours_restaurant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE restaurant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE restaurant_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE user_data_id_seq CASCADE');
        $this->addSql('ALTER TABLE dish DROP CONSTRAINT FK_957D8CB8B1E7706E');
        $this->addSql('ALTER TABLE dish DROP CONSTRAINT FK_957D8CB8C057AE07');
        $this->addSql('ALTER TABLE dish_ingridient DROP CONSTRAINT FK_DBA02D148EB0CB');
        $this->addSql('ALTER TABLE dish_ingridient DROP CONSTRAINT FK_DBA02D750B1398');
        $this->addSql('ALTER TABLE ingridient DROP CONSTRAINT FK_1C6DB80BB6CC2A71');
        $this->addSql('ALTER TABLE opening_hours_restaurant DROP CONSTRAINT FK_DD09684AB1E7706E');
        $this->addSql('ALTER TABLE restaurant DROP CONSTRAINT FK_EB95123F508D8F44');
        $this->addSql('ALTER TABLE restaurant_category DROP CONSTRAINT FK_26E9D72EB1E7706E');
        $this->addSql('ALTER TABLE restaurant_category DROP CONSTRAINT FK_26E9D72E12469DE2');
        $this->addSql('ALTER TABLE user_data DROP CONSTRAINT FK_D772BFAA79F37AE5');
        $this->addSql('ALTER TABLE user_data DROP CONSTRAINT FK_D772BFAACD4FDB16');
        $this->addSql('ALTER TABLE user_data DROP CONSTRAINT FK_D772BFAA320EF6E2');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE dish');
        $this->addSql('DROP TABLE dish_category');
        $this->addSql('DROP TABLE dish_ingridient');
        $this->addSql('DROP TABLE ingridient');
        $this->addSql('DROP TABLE ingridient_category');
        $this->addSql('DROP TABLE opening_hours_restaurant');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE restaurant_category');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_data');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
