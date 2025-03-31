<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324112022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, body VARCHAR(1000) NOT NULL, creation_date DATE NOT NULL, INDEX IDX_9474526C9D86650F (user_id), INDEX IDX_9474526CE85F12B8 (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(1000) NOT NULL, last_activity DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, forum_id INT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(5000) NOT NULL, creation_date DATE NOT NULL, last_activity DATE NOT NULL, INDEX IDX_5A8A6C8D9D86650F (user_id), INDEX IDX_5A8A6C8D67303880 (forum_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(180) NOT NULL, last_name VARCHAR(100) NOT NULL, first_name VARCHAR(100) NOT NULL, affiliation_location VARCHAR(250) DEFAULT NULL, specialization VARCHAR(250) DEFAULT NULL, research_topic VARCHAR(250) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_IDENTIFIER_ID (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Subscription (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_BBF7BF2BA76ED395 (user_id), INDEX IDX_BBF7BF2B4B89032C (post_id), PRIMARY KEY(user_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_like (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, comment_id INT NOT NULL, is_like TINYINT(1) NOT NULL, INDEX IDX_D6E20C7AA76ED395 (user_id), INDEX IDX_D6E20C7AF8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_questions (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, question VARCHAR(11) NOT NULL, answer VARCHAR(5000) NOT NULL, INDEX IDX_8A3CD931A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9D86650F FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE85F12B8 FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D9D86650F FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D67303880 FOREIGN KEY (forum_id) REFERENCES forum (id)');
        $this->addSql('ALTER TABLE Subscription ADD CONSTRAINT FK_BBF7BF2BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Subscription ADD CONSTRAINT FK_BBF7BF2B4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_like ADD CONSTRAINT FK_D6E20C7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_like ADD CONSTRAINT FK_D6E20C7AF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE user_questions ADD CONSTRAINT FK_8A3CD931A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C9D86650F');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE85F12B8');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D9D86650F');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D67303880');
        $this->addSql('ALTER TABLE Subscription DROP FOREIGN KEY FK_BBF7BF2BA76ED395');
        $this->addSql('ALTER TABLE Subscription DROP FOREIGN KEY FK_BBF7BF2B4B89032C');
        $this->addSql('ALTER TABLE user_like DROP FOREIGN KEY FK_D6E20C7AA76ED395');
        $this->addSql('ALTER TABLE user_like DROP FOREIGN KEY FK_D6E20C7AF8697D13');
        $this->addSql('ALTER TABLE user_questions DROP FOREIGN KEY FK_8A3CD931A76ED395');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE forum');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE Subscription');
        $this->addSql('DROP TABLE user_like');
        $this->addSql('DROP TABLE user_questions');
    }
}
