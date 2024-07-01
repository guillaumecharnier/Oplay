<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701191226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_game_key (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, users_id INT NOT NULL, game_key VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_71475C6CA76ED395 (user_id), INDEX IDX_71475C6CE48FD905 (game_id), INDEX IDX_71475C6C67B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_game_key ADD CONSTRAINT FK_71475C6CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_game_key ADD CONSTRAINT FK_71475C6CE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE user_game_key ADD CONSTRAINT FK_71475C6C67B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_232B318CE48FD905 ON game (game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_game_key DROP FOREIGN KEY FK_71475C6CA76ED395');
        $this->addSql('ALTER TABLE user_game_key DROP FOREIGN KEY FK_71475C6CE48FD905');
        $this->addSql('ALTER TABLE user_game_key DROP FOREIGN KEY FK_71475C6C67B3B43D');
        $this->addSql('DROP TABLE user_game_key');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CE48FD905');
        $this->addSql('DROP INDEX IDX_232B318CE48FD905 ON game');
        $this->addSql('ALTER TABLE game DROP game_id');
    }
}
