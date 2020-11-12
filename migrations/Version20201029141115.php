<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201029141115 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, level_id INT NOT NULL, name VARCHAR(120) NOT NULL, small_description LONGTEXT NOT NULL, full_description LONGTEXT NOT NULL, duration VARCHAR(60) NOT NULL, price DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, is_published TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, schedule VARCHAR(255) NOT NULL, program VARCHAR(255) NOT NULL, INDEX IDX_169E6FB912469DE2 (category_id), INDEX IDX_169E6FB95FB14BA7 (level_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course_level (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, prerequisite VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(120) NOT NULL, last_name VARCHAR(120) NOT NULL, image VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, last_log_at DATETIME NOT NULL, is_disabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB912469DE2 FOREIGN KEY (category_id) REFERENCES course_category (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB95FB14BA7 FOREIGN KEY (level_id) REFERENCES course_level (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB912469DE2');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB95FB14BA7');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE course_category');
        $this->addSql('DROP TABLE course_level');
        $this->addSql('DROP TABLE user');
    }
}
