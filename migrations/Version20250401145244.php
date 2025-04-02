<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250401145244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE taggable (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, entity_type VARCHAR(50) NOT NULL, tag_id INT NOT NULL, INDEX IDX_6243ADB9BAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE taggable ADD CONSTRAINT FK_6243ADB9BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('ALTER TABLE book_tag DROP FOREIGN KEY FK_F2F4CE1516A2B381');
        $this->addSql('ALTER TABLE book_tag DROP FOREIGN KEY FK_F2F4CE15BAD26311');
        $this->addSql('ALTER TABLE article_tag DROP FOREIGN KEY FK_919694F97294869C');
        $this->addSql('ALTER TABLE article_tag DROP FOREIGN KEY FK_919694F9BAD26311');
        $this->addSql('ALTER TABLE author_tag DROP FOREIGN KEY FK_E2CE630FBAD26311');
        $this->addSql('ALTER TABLE author_tag DROP FOREIGN KEY FK_E2CE630FF675F31B');
        $this->addSql('ALTER TABLE resource_tag DROP FOREIGN KEY FK_23D039CA89329D25');
        $this->addSql('ALTER TABLE resource_tag DROP FOREIGN KEY FK_23D039CABAD26311');
        $this->addSql('DROP TABLE book_tag');
        $this->addSql('DROP TABLE article_tag');
        $this->addSql('DROP TABLE author_tag');
        $this->addSql('DROP TABLE resource_tag');
        $this->addSql('ALTER TABLE article ADD author VARCHAR(255) NOT NULL, ADD link VARCHAR(255) NOT NULL, DROP content, DROP published_at');
        $this->addSql('ALTER TABLE author ADD birth_year INT DEFAULT NULL, ADD death_year INT DEFAULT NULL, ADD link VARCHAR(255) DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL, CHANGE bio nationality VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE book CHANGE link link VARCHAR(255) NOT NULL, CHANGE image image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE resource ADD description LONGTEXT DEFAULT NULL, ADD link VARCHAR(255) NOT NULL, CHANGE name title VARCHAR(255) NOT NULL, CHANGE url image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_tag (book_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_F2F4CE1516A2B381 (book_id), INDEX IDX_F2F4CE15BAD26311 (tag_id), PRIMARY KEY(book_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE article_tag (article_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_919694F97294869C (article_id), INDEX IDX_919694F9BAD26311 (tag_id), PRIMARY KEY(article_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE author_tag (author_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_E2CE630FBAD26311 (tag_id), INDEX IDX_E2CE630FF675F31B (author_id), PRIMARY KEY(author_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE resource_tag (resource_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_23D039CA89329D25 (resource_id), INDEX IDX_23D039CABAD26311 (tag_id), PRIMARY KEY(resource_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE book_tag ADD CONSTRAINT FK_F2F4CE1516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_tag ADD CONSTRAINT FK_F2F4CE15BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_tag ADD CONSTRAINT FK_919694F97294869C FOREIGN KEY (article_id) REFERENCES article (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_tag ADD CONSTRAINT FK_919694F9BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE author_tag ADD CONSTRAINT FK_E2CE630FBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE author_tag ADD CONSTRAINT FK_E2CE630FF675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resource_tag ADD CONSTRAINT FK_23D039CA89329D25 FOREIGN KEY (resource_id) REFERENCES resource (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resource_tag ADD CONSTRAINT FK_23D039CABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE taggable DROP FOREIGN KEY FK_6243ADB9BAD26311');
        $this->addSql('DROP TABLE taggable');
        $this->addSql('ALTER TABLE article ADD content LONGTEXT NOT NULL, ADD published_at DATETIME NOT NULL, DROP author, DROP link');
        $this->addSql('ALTER TABLE resource ADD name VARCHAR(255) NOT NULL, DROP title, DROP description, DROP link, CHANGE image url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE book CHANGE link link VARCHAR(255) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE author ADD bio VARCHAR(255) DEFAULT NULL, DROP birth_year, DROP death_year, DROP nationality, DROP link, DROP image');
    }
}
