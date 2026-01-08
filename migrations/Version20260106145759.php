<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260106145759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, location VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE movement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, destination VARCHAR(255) DEFAULT NULL, reference VARCHAR(100) DEFAULT NULL, notes CLOB DEFAULT NULL, created_at DATETIME NOT NULL, product_id INTEGER NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_F4DD95F74584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F4DD95F7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F4DD95F74584665A ON movement (product_id)');
        $this->addSql('CREATE INDEX IDX_F4DD95F7A76ED395 ON movement (user_id)');
        $this->addSql('CREATE TABLE personnel (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, hire_date DATETIME NOT NULL, last_modified_at DATETIME DEFAULT NULL, last_modification CLOB DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, department VARCHAR(100) DEFAULT NULL, office_number VARCHAR(50) DEFAULT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL, is_verified BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A6BCF3DEE7927C74 ON personnel (email)');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reference VARCHAR(255) NOT NULL, alert_threshold INTEGER NOT NULL, name VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, added_at DATETIME NOT NULL, supplier VARCHAR(255) NOT NULL, scent VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, entry_date DATETIME DEFAULT NULL, category_id INTEGER DEFAULT NULL, CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, hire_date DATE DEFAULT NULL, office VARCHAR(50) DEFAULT NULL, cin VARCHAR(20) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, is_active BOOLEAN NOT NULL, avatar VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE movement');
        $this->addSql('DROP TABLE personnel');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
