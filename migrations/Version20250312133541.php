<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250312133541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, chat_box_id INT NOT NULL, user VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, INDEX IDX_659DF2AABFCB4C1E (chat_box_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AABFCB4C1E FOREIGN KEY (chat_box_id) REFERENCES chat_box (id)');
        $this->addSql('ALTER TABLE chat_box ADD forum_id INT NOT NULL');
        $this->addSql('ALTER TABLE chat_box ADD CONSTRAINT FK_28636E9F29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id)');
        $this->addSql('CREATE INDEX IDX_28636E9F29CCBAD0 ON chat_box (forum_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AABFCB4C1E');
        $this->addSql('DROP TABLE chat');
        $this->addSql('ALTER TABLE chat_box DROP FOREIGN KEY FK_28636E9F29CCBAD0');
        $this->addSql('DROP INDEX IDX_28636E9F29CCBAD0 ON chat_box');
        $this->addSql('ALTER TABLE chat_box DROP forum_id');
    }
}
