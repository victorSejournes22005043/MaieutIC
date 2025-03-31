<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250331130016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment CHANGE body body VARCHAR(5000) NOT NULL');
        $this->addSql('ALTER TABLE forum ADD title VARCHAR(500) NOT NULL, ADD body VARCHAR(5000) NOT NULL, DROP name, DROP description');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum DROP FOREIGN KEY FK_852BBECD9D86650F');
        $this->addSql('DROP INDEX IDX_852BBECD9D86650F ON forum');
        $this->addSql('ALTER TABLE forum ADD name VARCHAR(50) NOT NULL, ADD description VARCHAR(1000) NOT NULL, DROP title, DROP body, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment CHANGE body body VARCHAR(1000) NOT NULL');
    }
}
