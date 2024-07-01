<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701192237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_game_key DROP FOREIGN KEY FK_71475C6C67B3B43D');
        $this->addSql('DROP INDEX IDX_71475C6C67B3B43D ON user_game_key');
        $this->addSql('ALTER TABLE user_game_key DROP users_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_game_key ADD users_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_game_key ADD CONSTRAINT FK_71475C6C67B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_71475C6C67B3B43D ON user_game_key (users_id)');
    }
}
